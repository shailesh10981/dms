<?php

namespace App\Http\Controllers;

use App\Models\ComplianceTemplate;
use App\Models\ComplianceTemplateField;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\ViewErrorBag; // ✅ add this at the top of controller
use Illuminate\Validation\ValidationException;

class ComplianceTemplateController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', ComplianceTemplate::class);

        $templates = ComplianceTemplate::with(['department', 'fields'])
            ->latest()
            ->paginate(20);

        return view('compliance.templates.index', compact('templates'));
    }

    public function create()
    {
        $this->authorize('create', ComplianceTemplate::class);

        $departments = Department::all();
        $fieldTypes = [
            'text' => 'Text',
            'number' => 'Number',
            'date' => 'Date',
            'select' => 'Dropdown Select',
            'checkbox' => 'Checkbox',
            'textarea' => 'Text Area',
        ];

        return view('compliance.templates.create', compact('departments', 'fieldTypes'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', ComplianceTemplate::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'frequency' => 'required|in:daily,weekly,monthly,quarterly,yearly,adhoc',
            'is_active' => 'boolean',
            'fields' => 'required|array|min:1',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.field_type' => 'required|in:text,number,date,select,checkbox,textarea',
            'fields.*.is_required' => 'boolean',
            'fields.*.options' => 'nullable|required_if:fields.*.field_type,select|string', // Changed from array to string
            'fields.*.validation_rules' => 'nullable|array',
        ]);

        // Create template
        $template = ComplianceTemplate::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'department_id' => $validated['department_id'],
            'frequency' => $validated['frequency'],
            'is_active' => $validated['is_active'] ?? false,
        ]);

        // Process and create fields
        foreach ($request->fields as $index => $field) {
            $fieldData = [
                'label' => $field['label'],
                'field_type' => $field['field_type'],
                'is_required' => $field['is_required'] ?? false,
                'order' => $index,
                'validation_rules' => $field['validation_rules'] ?? null,
            ];

            // Convert options from textarea to array for select fields
            if ($field['field_type'] === 'select' && isset($field['options'])) {
                $options = array_filter(
                    array_map('trim', explode("\n", $field['options'])),
                    function ($option) {
                        return !empty($option);
                    }
                );

                if (empty($options)) {
                    throw ValidationException::withMessages([
                        "fields.$index.options" => 'At least one option is required for select fields'
                    ]);
                }

                $fieldData['options'] = $options;
            }

            $template->fields()->create($fieldData);
        }

        return redirect()->route('compliance.templates.index')
            ->with('success', 'Template created successfully!');
    }

    public function show(ComplianceTemplate $complianceTemplate)
    {
        $this->authorize('view', $complianceTemplate);

        return view('compliance.templates.show', [
            'template' => $complianceTemplate->load('fields')
        ]);
    }

    public function edit(ComplianceTemplate $complianceTemplate)
    {
        $this->authorize('update', $complianceTemplate);

        $departments = Department::all();
        $fieldTypes = [
            'text' => 'Text',
            'number' => 'Number',
            'date' => 'Date',
            'select' => 'Dropdown Select',
            'checkbox' => 'Checkbox',
            'textarea' => 'Text Area',
        ];

        return view('compliance.templates.edit', [
            'template' => $complianceTemplate->load('fields'),
            'departments' => $departments,
            'fieldTypes' => $fieldTypes,
        ]);
    }

    public function update(Request $request, ComplianceTemplate $complianceTemplate)
    {
        $this->authorize('update', $complianceTemplate);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'frequency' => 'required|in:daily,weekly,monthly,quarterly,yearly,adhoc',
            'is_active' => 'boolean',
            'fields' => 'required|array|min:1',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.field_type' => 'required|in:text,number,date,select,checkbox,textarea',
            'fields.*.is_required' => 'boolean',
            'fields.*.options' => 'nullable|required_if:fields.*.field_type,select|array',
            'fields.*.options.*' => 'string|max:255',
            'fields.*.validation_rules' => 'nullable|array',
        ]);

        // Update template
        $complianceTemplate->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'department_id' => $validated['department_id'],
            'frequency' => $validated['frequency'],
            'is_active' => $validated['is_active'] ?? false,
        ]);

        // Sync fields
        $existingFieldIds = $complianceTemplate->fields->pluck('id')->toArray();
        $updatedFieldIds = [];

        foreach ($request->fields as $index => $field) {
            $fieldData = [
                'label' => $field['label'],
                'field_type' => $field['field_type'],
                'options' => $field['options'] ?? null,
                'is_required' => $field['is_required'] ?? false,
                'order' => $index,
                'validation_rules' => $field['validation_rules'] ?? null,
            ];

            if (isset($field['id']) && in_array($field['id'], $existingFieldIds)) {
                // Update existing field
                $complianceTemplate->fields()->where('id', $field['id'])->update($fieldData);
                $updatedFieldIds[] = $field['id'];
            } else {
                // Create new field
                $newField = $complianceTemplate->fields()->create($fieldData);
                $updatedFieldIds[] = $newField->id;
            }
        }

        // Delete removed fields
        $complianceTemplate->fields()
            ->whereNotIn('id', $updatedFieldIds)
            ->delete();

        return redirect()->route('compliance.templates.index')
            ->with('success', 'Template updated successfully!');
    }

    public function destroy(ComplianceTemplate $complianceTemplate)
    {
        $this->authorize('delete', $complianceTemplate);

        $complianceTemplate->delete();

        return redirect()->route('compliance.templates.index')
            ->with('success', 'Template deleted successfully!');
    }

    public function getFieldRow(Request $request)
    {
        try {
            $index = $request->index ?? 0;
            $fieldTypes = [
                'text' => 'Text',
                'number' => 'Number',
                'date' => 'Date',
                'select' => 'Dropdown Select',
                'checkbox' => 'Checkbox',
                'textarea' => 'Text Area',
            ];

            $field = (object)[
                'label' => '',
                'field_type' => 'text',
                'options' => [],
                'is_required' => false,
                'validation_rules' => []
            ];

            return response()->make(
                view('compliance.templates.partials.field-row', [
                    'index' => $index,
                    'field' => $field,
                    'fieldTypes' => $fieldTypes,
                    'errors' => session('errors') ?? new ViewErrorBag, // ✅ fixed
                ])->render(),
                200,
                ['Content-Type' => 'text/html']
            );
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
