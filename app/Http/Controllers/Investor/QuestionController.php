<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    /**
     * Store a newly created question in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'meeting_id' => 'required|exists:meetings,id',
            'question' => 'required|string|min:10|max:500',
        ]);

        $user = Auth::user();

        Question::create([
            'meeting_id' => $validated['meeting_id'],
            'investor_id' => $user->id,
            'question' => $validated['question'],
            'is_answered' => false,
        ]);

        return back()->with('success', 'Question submitted successfully.');
    }

    /**
     * Remove the specified question from storage.
     */
    public function destroy(Question $question)
    {
        $user = Auth::user();

        if ($question->investor_id !== $user->id) {
            return back()->with('error', 'You can only delete your own questions.');
        }

        $question->delete();

        return back()->with('success', 'Question deleted successfully.');
    }
}
