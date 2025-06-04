<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DiagramController extends Controller
{
    public function index()
    {
        return view('admin.diagrams.index');
    }

    public function classes()
    {
        return view('admin.diagrams.classes');
    }

    public function sequences()
    {
        return view('admin.diagrams.sequences');
    }

    public function packages()
    {
        return view('admin.diagrams.packages');
    }

    public function useCases()
    {
        return view('admin.diagrams.use-cases');
    }

    public function global()
    {
        return view('admin.diagrams.global');
    }

    public function permissions()
    {
        return view('admin.diagrams.permissions');
    }

    public function authentication()
    {
        return view('admin.diagrams.authentication');
    }

    public function meetings()
    {
        return view('admin.diagrams.meetings');
    }

    public function exports()
    {
        return view('admin.diagrams.exports');
    }
}
