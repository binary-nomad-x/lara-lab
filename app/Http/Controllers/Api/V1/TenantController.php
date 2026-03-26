<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TenantController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'tenant_name' => 'required|string|max:255',
            'domain' => 'required|string|unique:domains,domain',
            'user_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction();

            $slug = \Str::slug($validated['tenant_name']);
            
            // Allow duplicate slug suffix
            $originalSlug = $slug;
            $counter = 1;
            while (Tenant::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }

            $tenant = Tenant::create([
                'name' => $validated['tenant_name'],
                'slug' => $slug,
                'is_active' => true,
            ]);

            $domain = Domain::create([
                'tenant_id' => $tenant->id,
                'domain' => $validated['domain'],
                'is_primary' => true,
            ]);

            $user = User::create([
                'tenant_id' => $tenant->id,
                'name' => $validated['user_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Assign Super Admin role if using Spatie
            // $user->assignRole('Super Admin');

            DB::commit();

            return response()->json([
                'message' => 'Tenant created successfully.',
                'tenant' => $tenant,
                'user' => $user,
                'domain' => $domain
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Registration failed: ' . $e->getMessage()], 500);
        }
    }
}
