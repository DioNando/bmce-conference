<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    /**
     * Display a listing of the questions for a meeting.
     */
    public function index(Meeting $meeting)
    {
        $questions = $meeting->questions()->with('user')->get();

        return view('questions.index', compact('meeting', 'questions'));
    }

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
        $meeting = Meeting::findOrFail($validated['meeting_id']);

        // Check if user is authorized to add questions to this meeting
        // Either as an investor participant or the issuer
        $isAuthorized = $user->id === $meeting->issuer_id ||
                         $meeting->investors()->where('users.id', $user->id)->exists();

        if (!$isAuthorized) {
            return back()->with('error', 'You are not authorized to add questions to this meeting.');
        }

        Question::create([
            'meeting_id' => $validated['meeting_id'],
            'investor_id' => $user->id,
            'question' => $validated['question'],
            'is_answered' => false,
        ]);

        return back()->with('success', 'Question submitted successfully.');
    }

    /**
     * Update the specified question.
     */
    public function update(Request $request, Question $question)
    {
        $user = Auth::user();

        if ($question->investor_id !== $user->id) {
            return back()->with('error', 'You can only edit your own questions.');
        }

        $validated = $request->validate([
            'question' => 'required|string|min:10|max:500',
        ]);

        $question->update([
            'question' => $validated['question'],
        ]);

        return back()->with('success', 'Question updated successfully.');
    }

    /**
     * Remove the specified question from storage.
     */
    public function destroy(Question $question)
    {
        $user = Auth::user();

        // Allow deletion by the question creator or the meeting issuer
        $isAuthorized = $question->investor_id === $user->id ||
                         $question->meeting->issuer_id === $user->id;

        if (!$isAuthorized) {
            return back()->with('error', 'You are not authorized to delete this question.');
        }

        $question->delete();

        return back()->with('success', 'Question deleted successfully.');
    }

    /**
     * Admin can delete any question regardless of ownership
     */
    public function adminDestroy(Question $question)
    {
        // No authorization check needed since the admin route middleware
        // already ensures the user has admin role

        $question->delete();

        return back()->with('success', 'Question deleted successfully.');
    }

    /**
     * Mark a question as answered.
     */
    public function markAsAnswered(Question $question)
    {
        $user = Auth::user();
        $meeting = $question->meeting;

        // Only the issuer can mark questions as answered
        if ($meeting->issuer_id !== $user->id) {
            return back()->with('error', 'Only the meeting issuer can mark questions as answered.');
        }

        $question->is_answered = true;
        $question->save();

        return back()->with('success', 'Question marked as answered.');
    }

    /**
     * Mark a question as not answered.
     */
    public function markAsNotAnswered(Question $question)
    {
        $user = Auth::user();
        $meeting = $question->meeting;

        // Only the issuer can mark questions as not answered
        if ($meeting->issuer_id !== $user->id) {
            return back()->with('error', 'Only the meeting issuer can mark questions as not answered.');
        }

        $question->is_answered = false;
        $question->save();

        return back()->with('success', 'Question marked as not answered.');
    }
}
