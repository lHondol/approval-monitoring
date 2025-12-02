<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userService;
    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function view() {
        return view('user.view');
    }

    public function getData() {
        return $this->userService->getData();
    }

    public function getDetail() {
        
    }

    public function editForm() {

    }

    public function edit() {

    }

    public function remove() {

    }
}
