<?php

namespace App\Http\Controllers;

use App\Dtos\Input\StoreUserData;
use App\Dtos\Input\UpdateUserData;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Exceptions\SelfDeletionException;
use App\Services\Users\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService,
        private UserRepositoryInterface $userRepository
    ) {}

    public function index(): View
    {
        $users = $this->userRepository->paginateOrdered()->toPaginator();

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->userService->create(StoreUserData::fromRequest($request->validated()));

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function show(int $id): View
    {
        $user = $this->userRepository->findOrFail($id);

        return view('users.show', compact('user'));
    }

    public function edit(int $id): View
    {
        $user = $this->userRepository->findOrFail($id);

        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, int $id): RedirectResponse
    {
        $this->userService->update($id, UpdateUserData::fromRequest($request->validated()));

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->userService->delete($id, auth()->id());
        } catch (SelfDeletionException $e) {
            return redirect()->route('users.index')
                ->with('error', $e->getMessage());
        }

        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
