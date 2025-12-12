<?php

namespace App\Http\Controllers;

use App\Models\WaTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WaTemplateController extends Controller
{
    public function index(): View
    {
        $templates = WaTemplate::orderBy('title')->paginate(10);

        return view('wa-templates.index', compact('templates'));
    }

    public function create(): View
    {
        return view('wa-templates.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:100', 'unique:wa_templates,code'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        WaTemplate::create($validated);

        return redirect()->route('wa-templates.index')->with('success', 'Template WhatsApp berhasil dibuat.');
    }

    public function show(WaTemplate $wa_template): View
    {
        return view('wa-templates.show', ['template' => $wa_template]);
    }

    public function edit(WaTemplate $wa_template): View
    {
        return view('wa-templates.edit', ['template' => $wa_template]);
    }

    public function update(Request $request, WaTemplate $wa_template): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:100', 'unique:wa_templates,code,' . $wa_template->id],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $wa_template->update($validated);

        return redirect()->route('wa-templates.index')->with('success', 'Template WhatsApp diperbarui.');
    }

    public function destroy(WaTemplate $wa_template): RedirectResponse
    {
        $wa_template->delete();

        return redirect()->route('wa-templates.index')->with('success', 'Template dihapus.');
    }
}
