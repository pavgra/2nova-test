<?php namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;

class PageController
{
	function index()
	{
		return view('page.index', ['name' => 'Mom']);
	}
}