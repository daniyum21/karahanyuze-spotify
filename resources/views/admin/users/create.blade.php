@extends('layouts.app')

@section('title', 'Create User - Admin Dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black">
    <div class="container mx-auto px-4 py-12">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">Create New User</h1>
            <p class="text-zinc-400">Add a new user to the system</p>
        </div>

        <div class="max-w-2xl">
            @if ($errors->any())
            <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-zinc-900 rounded-lg p-8">
                <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="FirstName" class="block text-sm font-medium text-zinc-300 mb-2">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="FirstName" 
                                name="FirstName" 
                                value="{{ old('FirstName') }}"
                                required
                                class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            >
                        </div>

                        <div>
                            <label for="LastName" class="block text-sm font-medium text-zinc-300 mb-2">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="LastName" 
                                name="LastName" 
                                value="{{ old('LastName') }}"
                                required
                                class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="Email" class="block text-sm font-medium text-zinc-300 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            id="Email" 
                            name="Email" 
                            value="{{ old('Email') }}"
                            required
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        >
                    </div>

                    <div>
                        <label for="UserName" class="block text-sm font-medium text-zinc-300 mb-2">
                            Username <span class="text-zinc-500 text-xs">(Optional)</span>
                        </label>
                        <input 
                            type="text" 
                            id="UserName" 
                            name="UserName" 
                            value="{{ old('UserName') }}"
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        >
                        <p class="mt-1 text-xs text-zinc-500">If not provided, email will be used as username</p>
                    </div>

                    <div>
                        <label for="PublicName" class="block text-sm font-medium text-zinc-300 mb-2">
                            Public Name <span class="text-zinc-500 text-xs">(Optional)</span>
                        </label>
                        <input 
                            type="text" 
                            id="PublicName" 
                            name="PublicName" 
                            value="{{ old('PublicName') }}"
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        >
                        <p class="mt-1 text-xs text-zinc-500">If not provided, first name + last name will be used</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-zinc-300 mb-2">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                minlength="6"
                                maxlength="15"
                                class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            >
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-zinc-300 mb-2">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                required
                                minlength="6"
                                maxlength="15"
                                class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="RoleID" class="block text-sm font-medium text-zinc-300 mb-2">
                                Role <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="RoleID" 
                                name="RoleID" 
                                required
                                class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            >
                                <option value="2" {{ old('RoleID', 2) == 2 ? 'selected' : '' }}>Regular User</option>
                                <option value="1" {{ old('RoleID') == 1 ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>

                        <div>
                            <label for="StatusID" class="block text-sm font-medium text-zinc-300 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="StatusID" 
                                name="StatusID" 
                                required
                                class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            >
                                <option value="1" {{ old('StatusID', 1) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="2" {{ old('StatusID') == 2 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="flex items-center gap-2">
                            <input 
                                type="checkbox" 
                                name="email_verified" 
                                value="1"
                                {{ old('email_verified') ? 'checked' : '' }}
                                class="w-4 h-4 text-green-500 bg-zinc-800 border-zinc-700 rounded focus:ring-green-500"
                            >
                            <span class="text-sm text-zinc-300">Mark email as verified</span>
                        </label>
                    </div>

                    <div class="flex items-center gap-4">
                        <button 
                            type="submit" 
                            class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition-colors"
                        >
                            Create User
                        </button>
                        <a 
                            href="{{ route('admin.users.index') }}" 
                            class="px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-white font-semibold rounded-lg transition-colors"
                        >
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

