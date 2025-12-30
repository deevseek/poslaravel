<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-indigo-600 via-indigo-500 to-sky-500 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-indigo-500/20 transition hover:from-indigo-500 hover:via-indigo-500 hover:to-sky-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
