<?php

namespace App\Http\Controllers\Issuer;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    /**
     * Answer a question from an investor.
     */
    public function answer(Request $request, Question $question)
    {
        $user = Auth::user();

        if (!$user->hasRole(UserRole::ISSUER->value)) {
            return redirect()->route('home')
                ->with('error', 'You do not have permission to perform this action.');
        }

        // Check if the question is addressed to this issuer
        if ($question->meeting->issuer_id !== $user->id) {
            return redirect()->route('issuer.meetings.index')
                ->with('error', 'You do not have permission to answer this question.');
        }

        $validated = $request->validate([
            'response' => 'required|string|max:1000',
        ]);

        try {
            $question->response = $validated['response'];
            $question->answered_at = now();
            $question->is_answered = true;
            $question->save();

            return redirect()->back()
                ->with('success', 'Your response has been submitted.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to submit your response: ' . $e->getMessage())
                ->withInput();
        }
    }
}
