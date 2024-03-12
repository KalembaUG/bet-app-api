<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Exam;
use App\Models\User;
use App\Http\Resources\ExamResource;
use App\Http\Resources\ExamCollection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;

class ExamController extends Controller
{
    public function examsIndex(Request $request)
    {
        if (!Gate::allows('view-exams')) {
            return response(['msg' => 'Administrator access is required to perform this action.'], 403);
        }

        $order = $request->query('order') ?? 'DESC';
        $name = $request->query('name') ?? '';
        $location = $request->query('location') ?? '';
        $date = $request->query('date') ?? '';
        $month = $request->query('month') ?? '';
        $year = $request->query('year') ?? '';
        $after = $request->query('after') ?? '';

        return new ExamCollection(
            Exam::orderBy('date', $order)
                ->when($name, fn (Builder $query, string $name) => 
                    $query->where('candidate_name', env('USE_SQLITE_SYNTAX', 'ilike'), '%' . $name . '%')
                )
                ->when($location, fn (Builder $query, string $location) => 
                    $query->where('location_name', env('USE_SQLITE_SYNTAX', 'ilike'), '%' . $location . '%')
                )
                ->when($date, fn (Builder $query, string $date) => 
                    $query->whereDate('date', $date)
                )
                ->when($month, fn (Builder $query, string $month) => 
                    $query->whereMonth('date', $month)
                )
                ->when($year, fn (Builder $query, string $year) => 
                    $query->whereYear('date', $year)
                )
                ->when($after, fn (Builder $query, string $after) => 
                    $query->whereDate('date', '>', $after)
                )
                ->paginate($request->query('limit') ?? 30)
        );
    }


    public function store(Request $request)
    {
        Gate::authorize('create-exam');

        $request->validate([
            "title" => 'required|string',
            "description" => 'required|string',
            "candidate_id" => 'required|numeric',
            "candidate_name" => 'required|string',
            "date" => 'required|date',
            "location_name" => 'required|string',
            "latitude" => 'required|decimal:0,25',
            "longitude" => 'required|decimal:0,25'
        ]);

        $examFromDb = DB::table('exams')
                         ->where([
                            ['candidate_name', '=', $request->candidate_name],
                            ['date', '=', $request->date]
                         ])->first();
        
        $userFromDb = User::where('id', $request->candidate_id)->first();

        if (!$userFromDb)
        {
            return response()->json([
                'msg' => 'That user does not exist in the database.'
            ], 404);
        }
        else if ($examFromDb)
        {
            return response()->json([
                'msg' => 'Candidate is already booked in for an exam at this time.'
            ], 400);
        }
        else if ($userFromDb->name != $request->candidate_name)
        {
            return response()->json([
                'msg' => 'Candidate\'s name does not match his or her existing name on record.'
            ], 400);
        }
        else
        {
            return Exam::create($request->all());
        }
    }

    
    public function show(string $id)
    {
        $exam = Exam::find($id);
        if (!$exam || !Gate::allows('view-single-exam', $exam->candidate_id)) {
            return response(['msg' => 'Not found.'], 404);
        }

        return new ExamResource(Exam::find($id));
    }

    /**
     * searches for records whose name attribute matches the provided substring
     * @param str $name
     * @return \Illuminate\Http\Response
     */
    public function search(string $name)
    {
        return new ExamCollection(
            Exam::where('candidate_name', env('USE_SQLITE_SYNTAX', 'ilike'), '%' . $name . '%')->get()
        );
    }

    
    public function update(Request $request, string $id)
    {
        $exam = Exam::find($id);

        if (!$exam || !Gate::allows('update-exam', $exam->candidate_id)) {
            return response(['msg' => 'Not found.'], 404);
        }

        if ($request->filled('candidate_name') || $request->filled('candidate_id')){
            return response(['msg' => 'You cannot change a candidate\'s name or ID.'], 400);
        }

        $exam->update($request->all());
        return $exam;
    }

    
    public function destroy(string $id)
    {
        $exam = Exam::find($id);

        if (!$exam || !Gate::allows('delete-exam', $exam->candidate_id)) {
            return response(['msg' => 'Not found.'], 404);
        }

        return Exam::destroy($id);
    }
}
