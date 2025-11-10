<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaterialController extends Controller
{
    private array $categories = [
        'Materiales Preventivos',
        'Materiales Restaurativos',
        'Materiales Auxiliares',
        'Metálicos',
        'Poliméricos',
        'Cerámicos',
        'Composites',
    ];
    public function index(Request $request)
    {
        $user = Auth::user();
        $q = trim((string)$request->query('q', ''));
        $cat = trim((string)$request->query('category', ''));
        $month = trim((string)$request->query('month', ''));

        // Shared scope: list all materials
        $query = Material::query();
        if ($q !== '') {
            $query->where(function($x) use ($q) {
                $x->where('name', 'like', "%$q%")
                  ->orWhere('notes', 'like', "%$q%")
                  ->orWhere('category', 'like', "%$q%");
            });
        }
        if ($cat !== '') {
            $query->where('category', $cat);
        }

        if ($month !== '') {
            // Espera formato YYYY-MM
            try {
                [$y,$m] = explode('-', $month);
                $start = \Carbon\Carbon::createFromDate((int)$y, (int)$m, 1)->startOfMonth();
                $end = (clone $start)->endOfMonth();
                $query->whereBetween('created_at', [$start, $end]);
            } catch (\Throwable $e) {
                // ignorar formato inválido
            }
        }

        // Ordenar por fecha de creación desc para agrupación mensual automática
        $query->with('user')->orderBy('created_at', 'desc')->orderBy('name');
        $materials = $query->paginate(15)->appends($request->query());
        $categories = $this->categories; // categorías fijas
        $grouped = $materials->getCollection()->groupBy(function($m){ return optional($m->created_at)->format('Y-m'); });
        return view('user.inventory', [
            'materials' => $materials,
            'categories' => $categories,
            'q' => $q,
            'cat' => $cat,
            'month' => $month,
            'grouped' => $grouped,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'category' => ['nullable','string','in:Materiales Preventivos,Materiales Restaurativos,Materiales Auxiliares,Metálicos,Poliméricos,Cerámicos,Composites'],
            'quantity' => ['required','integer','min:0'],
            'min_quantity' => ['nullable','integer','min:0'],
            'unit' => ['nullable','string','max:30'],
            'notes' => ['nullable','string','max:1000'],
        ]);
        $data['user_id'] = $user->id;
        Material::create($data);
        return redirect()->route('materials.index')->with('ok','Material agregado');
    }

    public function destroy(Material $material)
    {
        // Shared deletion allowed for authenticated users
        $material->delete();
        return redirect()->route('materials.index')->with('ok','Material eliminado');
    }

    public function update(Request $request, Material $material)
    {
        $user = Auth::user();
        // Shared editing allowed for authenticated users
        $data = $request->validate([
            'field' => ['required','in:name,category,quantity,min_quantity,unit,notes'],
            'value' => ['nullable','string','max:1000'],
        ]);
        $field = $data['field'];
        $value = $data['value'];
        if ($field === 'quantity' || $field === 'min_quantity') {
            $request->validate(['value' => ['required','integer','min:0']]);
            $value = (int) $value;
        } elseif ($field === 'category') {
            $request->validate(['value' => ['nullable','in:Materiales Preventivos,Materiales Restaurativos,Materiales Auxiliares,Metálicos,Poliméricos,Cerámicos,Composites']]);
        }
        $material->{$field} = $value;
        $material->save();
        return response()->json(['ok' => true]);
    }

    public function lowCount(Request $request)
    {
        $user = Auth::user();
        // Shared: count across all materials
        $count = Material::query()
            ->whereColumn('quantity', '<', 'min_quantity')
            ->count();
        return response()->json(['low' => $count]);
    }

    public function stats(Request $request)
    {
        $user = Auth::user();
        $year = (int)($request->query('year', now()->year));
        $month = (int)($request->query('month', 0)); // 1..12 or 0 = whole year
        $category = trim((string)$request->query('category', ''));

        if ($month >= 1 && $month <= 12) {
            $start = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
            $end = (clone $start)->endOfMonth();
            $q = Material::query()
                ->whereBetween('created_at', [$start, $end]);
            if ($category !== '') { $q->where('category', $category); }
            // Sum of quantities added per day
            $rows = $q->selectRaw('DATE(created_at) as d, SUM(quantity) as qty')
                ->groupBy('d')->orderBy('d')->pluck('qty','d');
            $labels = [];$values=[];$cursor=(clone $start);
            while ($cursor <= $end) {
                $d=$cursor->toDateString();
                $labels[]=$cursor->format('d');
                $values[]=(int)($rows[$d] ?? 0);
                $cursor->addDay();
            }
            return response()->json(['granularity'=>'day','labels'=>$labels,'values'=>$values]);
        } else {
            $start = \Carbon\Carbon::create($year, 1, 1)->startOfYear();
            $end = (clone $start)->endOfYear();
            $q = Material::where('user_id', $user->id)
                ->whereBetween('created_at', [$start, $end]);
            if ($category !== '') { $q->where('category', $category); }
            $rows = $q->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, SUM(quantity) as qty")
                ->groupBy('ym')->orderBy('ym')->pluck('qty','ym');
            $labels=[];$values=[];
            for($m=1;$m<=12;$m++){
                $ym = \Carbon\Carbon::create($year,$m,1)->format('Y-m');
                $labels[] = \Carbon\Carbon::create($year,$m,1)->locale('es')->isoFormat('MMM');
                $values[] = (int)($rows[$ym] ?? 0);
            }
            return response()->json(['granularity'=>'month','labels'=>$labels,'values'=>$values]);
        }
    }

    public function exportCsv(Request $request)
    {
        $filename = 'materials_' . now()->format('Ymd_His') . '.csv';
        $columns = ['Nombre','Categoría','Cantidad','Mínimo','Unidad','Notas','Registrado por','Creado'];
        $callback = function() use ($columns) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            Material::with('user')->orderBy('created_at','desc')->chunk(500, function($chunk) use ($out) {
                foreach ($chunk as $m) {
                    fputcsv($out, [
                        (string)$m->name,
                        (string)($m->category ?? ''),
                        (string)($m->quantity ?? 0),
                        (string)($m->min_quantity ?? 0),
                        (string)($m->unit ?? ''),
                        (string)($m->notes ?? ''),
                        optional($m->user)->name ?? '',
                        optional($m->created_at)->format('Y-m-d H:i'),
                    ]);
                }
            });
            fclose($out);
        };
        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
