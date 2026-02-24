<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Manajemen Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm mb-6 flex justify-between items-center text-gray-900 dark:text-gray-100">
                <div>
                    <h1 class="text-3xl font-bold mb-1">Manajemen Admin</h1>
                    <p class="text-gray-500 dark:text-gray-400">Khusus AdminDev</p>
                </div>
                <a href="{{ route('admin.users.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-medium transition">Tambah
                    Admin</a>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden text-gray-900 dark:text-gray-100">
                <table class="w-full text-left">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="p-4 font-semibold text-gray-700 dark:text-gray-300">Nama</th>
                            <th class="p-4 font-semibold text-gray-700 dark:text-gray-300">Email</th>
                            <th class="p-4 font-semibold text-gray-700 dark:text-gray-300 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins as $admin)
                            <tr
                                class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="p-4">{{ $admin->name }}</td>
                                <td class="p-4">{{ $admin->email }}</td>
                                <td class="p-4 flex gap-2 justify-center">
                                    <a href="{{ route('admin.users.edit', $admin) }}"
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm transition">Edit</a>
                                    <form action="{{ route('admin.users.destroy', $admin) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus admin ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-4 text-center text-gray-500 dark:text-gray-400">Belum ada data
                                    admin.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>