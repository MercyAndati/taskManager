<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    //create task
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'=>[
                'required',
                'string',
                Rule::unique('tasks')->where(function ($query) use ($request){
                    return $query->where('due_date',$request->due_date);
                })
            ],
            'due_date'=>'required|date|after_or_equal:today',
            'priority'=>['required',Rule::in(['low','medium','high'])],
        ]);

        $task = Task::create([
            'title'=>$validated['title'],
            'due_date'=>$validated['due_date'],
            'priority'=>$validated['priority'],
            'status'=>'pending'
        ]);

        return response()->json($task, 201);
    }

    //List Tasks
    public function index(Request $request){
        $query = Task::query();
        if($request->has('status')){
            $query->where('status', $request->status);
        }

        $tasks = $query->orderByRaw("FIELD(priority, 'high','medium','low')")
        ->orderBy('due_date','asc')
        ->get();
        if($tasks->isEmpty()){
            return response()->json(['message'=>'No tasks found'], 200);
        }

        return response()->json($tasks);
    }

    //Update task status
    public function updateStatus(Request $request, $id){
        $task = Task::findOrFail($id);

        $request->validate([
            'status'=>['required', Rule::in(['in_progress', 'done'])]
        ]);

        //prevent skipping or reverting
        $validTransitions =[
            'pending'=>'in_progress',
            'in_progress'=>'done'
        ];

        if(!isset($validTransitions[$task->status])||$validTransitions[$task->status]!==$request->status){
            return response()->json(['errir'=>'Invalid status transition.'], 400);
        }

        $task->update(['status'=>$request->status]);

        return response()->json($task);
    }

    //Delete task
    public function destroy($id){
        $task=Task::findOrFail($id);

        if($task->status !== 'done'){
            return response()->json(['error'=>'Forbidden. Only done tasks can be deleted.'],403);
        }

        $task->delete();

        return response()->json(['message'=>'Task deleted succesfully.']);
    }

    //Daily report
    public function report(Request $request)
    {
        $request->validate(['date' => 'required|date']);
        $date = $request->query('date');

        $tasks = Task::whereDate('due_date', $date)->get();

        $summary = [
            'high' => ['pending' => 0, 'in_progress' => 0, 'done' => 0],
            'medium' => ['pending' => 0, 'in_progress' => 0, 'done' => 0],
            'low' => ['pending' => 0, 'in_progress' => 0, 'done' => 0],
        ];

        foreach ($tasks as $task) {
            $summary[$task->priority][$task->status]++;
        }

        return response()->json([
            'date' => $date,
            'summary' => $summary
        ]);
    }
}
