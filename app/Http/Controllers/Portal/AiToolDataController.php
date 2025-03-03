<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AiTool;
use Illuminate\Support\Facades\Storage;
use App\Models\AiToolsCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AiToolDataController extends Controller
{

    public function addEdit()
    {

        $categories = AiToolsCategory::all();
        $selectedCategories = [];
        return view('ai-tools.add-edit', compact('categories', 'selectedCategories'));
    }

    public function store(Request $request)
    {

        DB::beginTransaction();
        try {

            $request->validate([
                'slug' => 'required|unique:ai_tools,slug',
                'name' => 'required|string|max:255',
                'category' => 'required|array',
                'category.*' => 'exists:ai_tool_category,id',
                'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'cover' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
                'short_description_heading' => 'nullable|string',
                'short_description' => 'nullable|string',
                'release_date'  => 'nullable',
                'verified_status' => 'nullable|boolean',
                'integration_capabilities' => 'nullable|boolean',
                'payment_text' => 'nullable|string|max:255',
                'payment_status' => 'required|in:1,2',
                'platform_compatibility'    => 'required',
                'website_link' => 'nullable|url|max:255',
                'description_heading' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'key_features' => 'nullable|string',
                'pros' => 'nullable|array',
                'cons' => 'nullable|array',
                'voila_description' => 'nullable|string',
                'long_description' => 'nullable|string',
                'aitool_filter' => 'nullable|string|max:255',
            ]);

            $categories = is_array($request->category) ? $request->category : [];

            $validatedData = $request->only([
                'slug', 'name', 'short_description_heading', 'short_description', 'release_date' ,
                'verified_status', 'integration_capabilities' , 'payment_text', 'payment_status' , 'platform_compatibility' , 'website_link', 'description_heading',
                'description', 'key_features', 'pros', 'cons', 'voila_description', 'long_description',
                'aitool_filter', 'added_by'
            ]);

            $platform_compatibility = $request->platform_compatibility;
            $platform_compatibility = implode(",", $platform_compatibility);
            $validatedData['platform_compatibility'] = $platform_compatibility;
            $validatedData['added_by'] = auth()->id();
            $validatedData['slug'] = Str::slug($validatedData['slug']);

            // Handle pros data if available and format it as required
            if ($request->has('pros')) {

                $pros = $request->pros;
                $formattedPros = [];
    
                foreach ($pros['title'] as $index => $title) {

                    $formattedPros["pros_{$index}"] = [
                        'heading' => $title,
                        'description' => isset($pros['content'][$index]) ? $pros['content'][$index] : '', // Default to empty string if no content
                    ];
                }
            
                $formattedJson = json_encode($formattedPros, JSON_UNESCAPED_UNICODE);

                // Serialize the formatted pros array before saving
                $validatedData['pros'] = "\"" . str_replace('"', '\\"', $formattedJson) . "\"";
            }

            // Handle cons data if available and format it as required
            if ($request->has('cons')) {

                $cons = $request->cons;
                $formattedCons = [];

                foreach ($cons['title'] as $index => $title) {

                    $formattedCons["cons_{$index}"] = [
                        'heading' => $title,
                        'description' => isset($cons['content'][$index]) ? $cons['content'][$index] : '', // Default to empty string if no content
                    ];
                }

                $formattedJson = json_encode($formattedCons, JSON_UNESCAPED_UNICODE);

                // Serialize the formatted cons array before saving
                $validatedData['cons'] = "\"" . str_replace('"', '\\"', $formattedJson) . "\"";
            }

            // Upload Images
            if ($request->hasFile('logo')) {

                $image = $request->file('logo');
                $formattedDate = Carbon::now()->timestamp; 
                $extension = $image->getClientOriginalExtension(); // Get file extension
                $imageName = 'ai_tool_logo_' . $formattedDate . '.' . $extension; // New file name with formatted date-time
                $logoPath = $image->storeAs('ai-tools-images', $imageName, 'public');
                $validatedData['logo'] = $imageName;
            }

            if ($request->hasFile('cover')) {

                $image = $request->file('cover');
                $formattedDate = Carbon::now()->timestamp; 
                $extension = $image->getClientOriginalExtension(); // Get file extension
                $imageName = 'ai_tool_cover_' . $formattedDate . '.' . $extension; // New file name with formatted date-time
                $coverPath = $image->storeAs('ai-tools-images', $imageName, 'public');
                $validatedData['cover'] = $imageName;
            }

            if ($request->filled('key_features')) {

                $validatedData['key_features'] = substr(strip_tags($request->key_features), 0, 65000);
            }

            // Save to Database
            $tool = AiTool::create($validatedData);

            if (!empty($categories)) {

                $tool->category()->attach($categories);
            }

            DB::commit();

            return redirect()->route('ai-tools.list')->with('success', 'AI Tool submitted successfully!');
        } catch (\Exception $e) {

            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to submit AI Tool: ' . $e->getMessage());
        }
    }

    public function list(Request $request)
    {

        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'desc');
        $categories = AiToolsCategory::all();

        $aiTools = AiTool::with('category')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy($sortBy, $sortDirection)
            ->paginate(10);

        // Check if the request is AJAX
        if ($request->ajax()) {
            
            return response()->json([
                'html' => view('ai-tools.list', compact('aiTools'))->render(),
                'pagination' => (string) $aiTools->appends($request->all())->links()
            ]);
        }

        return view('ai-tools.list', compact('categories', 'aiTools', 'search', 'sortBy', 'sortDirection'));
    }

    public function edit($id)
    {

        $tool = AiTool::with('category')->findOrFail($id);
        $categories = AiToolsCategory::all();
        $selectedCategories = $tool->category->pluck('id')->toArray();

        // Decode JSON data for pros
        $pros = json_decode(json_decode($tool->pros, true), true);

        // Decode JSON data for cons
        $cons = json_decode(json_decode($tool->cons, true), true);

        return view('ai-tools.add-edit', compact('tool', 'categories', 'selectedCategories', 'pros', 'cons'));
    }

    public function update(Request $request, $id)
    {

        DB::beginTransaction();
        try {

            $request->validate([
                'slug' => 'required|unique:ai_tools,slug,' . $id,
                'name' => 'required|string|max:255',
                'category' => 'required|array',
                'category.*' => 'exists:ai_tool_category,id',
                'short_description_heading' => 'nullable|string|max:255',
                'short_description' => 'nullable|string',
                'release_date'  => 'nullable',
                'verified_status' => 'nullable|boolean',
                'integration_capabilities' => 'nullable|boolean',
                'payment_text' => 'nullable|string|max:255',
                'payment_status' => 'required|in:1,2',
                'platform_compatibility'    => 'required',
                'website_link' => 'nullable|url',
                'description_heading' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'key_features' => 'nullable|string',
                'pros' => 'nullable|array',
                'cons' => 'nullable|array',
                'voila_description' => 'nullable|string',
                'long_description' => 'nullable|string',
                'aitool_filter' => 'nullable|string|max:255',
                'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'cover' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            ]);

            $tool = AiTool::findOrFail($id);
            $validatedData = $request->except(['logo', 'cover']);
            $platform_compatibility = $request->platform_compatibility;
            $platform_compatibility = implode(",", $platform_compatibility);
            $validatedData['platform_compatibility'] = $platform_compatibility;
            $validatedData['added_by'] = auth()->id(); // Automatically set added_by
            $validatedData['slug'] = Str::slug($request->input('slug'));

            // Handle Pros Formatting
            if ($request->has('pros')) {

                $pros = $request->pros;
                $formattedPros = [];

                foreach ($pros['title'] as $index => $title) {

                    $formattedPros["pros_{$index}"] = [
                        'heading' => $title,
                        'description' => isset($pros['content'][$index]) ? $pros['content'][$index] : '',
                    ];
                }

                $formattedJson = json_encode($formattedPros, JSON_UNESCAPED_UNICODE);

                // Serialize the formatted pros array before saving
                $validatedData['pros'] = "\"" . str_replace('"', '\\"', $formattedJson) . "\"";
            }

            // Handle Cons Formatting
            if ($request->has('cons')) {

                $cons = $request->cons;
                $formattedCons = [];

                foreach ($cons['title'] as $index => $title) {

                    $formattedCons["cons_{$index}"] = [
                        'heading' => $title,
                        'description' => isset($cons['content'][$index]) ? $cons['content'][$index] : '',
                    ];
                }
                
                $formattedJson = json_encode($formattedCons, JSON_UNESCAPED_UNICODE);

                // Serialize the formatted cons array before saving
                $validatedData['cons'] = "\"" . str_replace('"', '\\"', $formattedJson) . "\"";
            }

            // Handle image upload for logo
            if ($request->hasFile('logo')) {

                // Delete old logo
                if ($tool->logo) {

                    Storage::disk('public')->delete($tool->logo);
                }

                $image = $request->file('logo');
                $formattedDate = Carbon::now()->timestamp; // Standard timestamp format
                $extension = $image->getClientOriginalExtension();
                $imageName = 'ai_tool_logo_' . $formattedDate . '.' . $extension;
                $logoPath = $image->storeAs('ai-tools-images', $imageName, 'public');
                $validatedData['logo'] = $imageName;
            }

            // Handle image upload for cover
            if ($request->hasFile('cover')) {

                // Delete old cover
                if ($tool->cover) {

                    Storage::disk('public')->delete($tool->cover);
                }

                $image = $request->file('cover');
                $formattedDate = Carbon::now()->timestamp; // Standard timestamp format
                $extension = $image->getClientOriginalExtension();
                $imageName = 'ai_tool_cover_' . $formattedDate . '.' . $extension;
                $coverPath = $image->storeAs('ai-tools-images', $imageName, 'public');
                $validatedData['cover'] = $imageName;
            }

            // Trim key_features length if needed
            if ($request->filled('key_features')) {

                $validatedData['key_features'] = substr(strip_tags($request->key_features), 0, 65000);
            }
            
            // Update record
            $tool->update($validatedData);

            if (!empty($request->category)) {

                $tool->category()->sync($request->category);
            }

            DB::commit();

            return redirect()->route('ai-tools.list')->with('success', 'AI Tool updated successfully!');
        } catch (\Exception $e) {

            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to submit AI Tool: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {

        DB::beginTransaction();
        try {

            $tool = AiTool::findOrFail($id);

            if (!is_null($tool)) {

                $tool->delete();
            }

            DB::commit();

            return redirect()->back()->with('success', 'Tool deleted successfully.');
        } catch (\Exception $e) {

            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete tool: ' . $e->getMessage());
        }
    }

    public function view($id)
    {

        $tool = AiTool::findOrFail($id);
        return view('ai-tools.view', compact('tool'));
    }
}