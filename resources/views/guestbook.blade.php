<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Buku Tamu') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-neutral-100 text-neutral-900 antialiased">
        <div class="mx-auto w-full max-w-3xl px-5 py-10 sm:py-16">
            <header class="mb-8 text-center">
                <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">{{ config('app.name', 'Buku Tamu') }}</h1>
                <p class="mt-2 text-neutral-600">
                    Tinggalkan nama dan pesanmu. Sudah ada
                    <span class="font-semibold text-neutral-800">{{ $total }}</span> pesan.
                </p>
            </header>

            @if (session('success'))
                <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            <section class="mb-10 rounded-xl bg-white p-6 shadow-sm ring-1 ring-neutral-200">
                <h2 class="mb-4 text-lg font-semibold">Isi buku tamu</h2>

                <form method="POST" action="{{ route('guestbook.store') }}" class="space-y-4">
                    @csrf
                    <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">

                    <div>
                        <label for="name" class="mb-1 block text-sm font-medium">Nama</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            maxlength="60"
                            required
                            placeholder="Nama kamu"
                            class="w-full rounded-md border border-neutral-300 px-3 py-2 focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900"
                        >
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="message" class="mb-1 block text-sm font-medium">Pesan</label>
                        <textarea
                            id="message"
                            name="message"
                            rows="3"
                            maxlength="500"
                            required
                            placeholder="Tulis pesan, kesan, atau salam"
                            class="w-full rounded-md border border-neutral-300 px-3 py-2 focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900"
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-md bg-neutral-900 px-4 py-2.5 font-medium text-white transition hover:bg-neutral-700 sm:w-auto"
                    >
                        Kirim pesan
                    </button>
                </form>
            </section>

            <section>
                <h2 class="mb-4 text-lg font-semibold">Pesan terbaru</h2>

                @if ($entries->isEmpty())
                    <div class="rounded-xl border border-dashed border-neutral-300 bg-white/60 px-6 py-10 text-center text-neutral-500">
                        Belum ada pesan. Jadi yang pertama mengisi buku tamu ini.
                    </div>
                @else
                    <ul class="space-y-3">
                        @foreach ($entries as $entry)
                            <li class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-neutral-200">
                                <div class="flex items-baseline justify-between gap-4">
                                    <p class="font-semibold">{{ $entry->name }}</p>
                                    <time class="shrink-0 text-xs text-neutral-500" datetime="{{ $entry->created_at->toIso8601String() }}">
                                        {{ $entry->created_at->format('d M Y, H:i') }}
                                    </time>
                                </div>
                                <p class="mt-2 whitespace-pre-line break-words text-neutral-700">{{ $entry->message }}</p>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-6">
                        {{ $entries->links() }}
                    </div>
                @endif
            </section>

            <footer class="mt-12 text-center text-xs text-neutral-500">
                <p>Jam situs: {{ now()->format('d M Y, H:i') }} UTC · Buku tamu digital</p>
            </footer>
        </div>
    </body>
</html>
