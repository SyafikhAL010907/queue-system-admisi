<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Tambah Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm mb-6 text-gray-900 dark:text-gray-100">
                <h1 class="text-3xl font-bold mb-4">Tambah Admin Baru</h1>

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-800 p-4 rounded-xl mb-4">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.users.store') }}" method="POST" class="flex flex-col gap-4">
                    @csrf
                    <div>
                        <label class="block font-medium mb-1 text-gray-700 dark:text-gray-300">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="border border-gray-300 dark:border-gray-700 dark:bg-gray-900 p-3 rounded-xl w-full focus:ring-2 focus:ring-blue-400 outline-none"
                            required>
                    </div>
                    <div>
                        <label class="block font-medium mb-1 text-gray-700 dark:text-gray-300">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="border border-gray-300 dark:border-gray-700 dark:bg-gray-900 p-3 rounded-xl w-full focus:ring-2 focus:ring-blue-400 outline-none"
                            required>
                    </div>
                    <div>
                        <label class="block font-medium mb-1 text-gray-700 dark:text-gray-300">Password</label>
                        <input type="password" name="password"
                            class="border border-gray-300 dark:border-gray-700 dark:bg-gray-900 p-3 rounded-xl w-full focus:ring-2 focus:ring-blue-400 outline-none"
                            required>
                    </div>
                    <div>
                        <label class="block font-medium mb-1 text-gray-700 dark:text-gray-300">Konfirmasi
                            Password</label>
                        <input type="password" name="password_confirmation"
                            class="border border-gray-300 dark:border-gray-700 dark:bg-gray-900 p-3 rounded-xl w-full focus:ring-2 focus:ring-blue-400 outline-none"
                            required>
                    </div>

                    <div class="flex gap-3 mt-4">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-medium transition">Simpan</button>
                        <a href="{{ route('admin.users.index') }}"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200 px-6 py-3 rounded-xl font-medium transition flex items-center">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>