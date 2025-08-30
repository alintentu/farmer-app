<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Module\Models\Module;
use App\Domain\Tenant\Models\Tenant;
use App\Domain\User\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AtlasSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPlans();
        $this->seedModules();
        $this->seedDemoTenant();
    }

    private function seedPlans(): void
    {
        $plans = [
            [
                'key' => 'starter',
                'name' => 'Starter',
                'description' => 'Perfect for small teams getting started',
                'price' => 5.00,
                'billing_cycle' => 'monthly',
                'features' => [
                    'tasks' => true,
                    'invoicing' => true,
                    'users_limit' => 3,
                    'storage_limit' => '1GB'
                ],
                'limits' => [
                    'users' => 3,
                    'projects' => 5,
                    'storage' => 1073741824 // 1GB in bytes
                ],
                'sort_order' => 1
            ],
            [
                'key' => 'solo',
                'name' => 'Solo',
                'description' => 'For individual professionals',
                'price' => 10.00,
                'billing_cycle' => 'monthly',
                'features' => [
                    'tasks' => true,
                    'invoicing' => true,
                    'docs' => true,
                    'users_limit' => 1,
                    'storage_limit' => '5GB'
                ],
                'limits' => [
                    'users' => 1,
                    'projects' => 10,
                    'storage' => 5368709120 // 5GB in bytes
                ],
                'sort_order' => 2
            ],
            [
                'key' => 'team',
                'name' => 'Team',
                'description' => 'For growing teams',
                'price' => 20.00,
                'billing_cycle' => 'monthly',
                'features' => [
                    'tasks' => true,
                    'invoicing' => true,
                    'docs' => true,
                    'crm' => true,
                    'users_limit' => 10,
                    'storage_limit' => '10GB'
                ],
                'limits' => [
                    'users' => 10,
                    'projects' => 25,
                    'storage' => 10737418240 // 10GB in bytes
                ],
                'sort_order' => 3
            ],
            [
                'key' => 'growth',
                'name' => 'Growth',
                'description' => 'For scaling businesses',
                'price' => 50.00,
                'billing_cycle' => 'monthly',
                'features' => [
                    'tasks' => true,
                    'invoicing' => true,
                    'docs' => true,
                    'crm' => true,
                    'marketing' => true,
                    'automation' => true,
                    'users_limit' => 25,
                    'storage_limit' => '25GB'
                ],
                'limits' => [
                    'users' => 25,
                    'projects' => 100,
                    'storage' => 26843545600 // 25GB in bytes
                ],
                'sort_order' => 4
            ],
            [
                'key' => 'scale',
                'name' => 'Scale',
                'description' => 'For large organizations',
                'price' => 100.00,
                'billing_cycle' => 'monthly',
                'features' => [
                    'tasks' => true,
                    'invoicing' => true,
                    'docs' => true,
                    'crm' => true,
                    'marketing' => true,
                    'automation' => true,
                    'analytics' => true,
                    'users_limit' => 100,
                    'storage_limit' => '100GB'
                ],
                'limits' => [
                    'users' => 100,
                    'projects' => 500,
                    'storage' => 107374182400 // 100GB in bytes
                ],
                'sort_order' => 5
            ],
            [
                'key' => 'enterprise',
                'name' => 'Enterprise',
                'description' => 'For enterprise customers',
                'price' => 2500.00,
                'billing_cycle' => 'monthly',
                'features' => [
                    'tasks' => true,
                    'invoicing' => true,
                    'docs' => true,
                    'crm' => true,
                    'marketing' => true,
                    'automation' => true,
                    'analytics' => true,
                    'helpdesk' => true,
                    'users_limit' => 'unlimited',
                    'storage_limit' => 'unlimited'
                ],
                'limits' => [
                    'users' => -1, // unlimited
                    'projects' => -1, // unlimited
                    'storage' => -1 // unlimited
                ],
                'sort_order' => 6
            ]
        ];

        foreach ($plans as $plan) {
            DB::table('plans')->updateOrInsert(
                ['key' => $plan['key']],
                $plan
            );
        }
    }

    private function seedModules(): void
    {
        $modules = [
            [
                'key' => 'tasks',
                'label' => 'Tasks & Projects',
                'description' => 'Manage tasks, projects, and team collaboration',
                'icon' => 'CheckCircleIcon',
                'defaults' => [
                    'settings' => [
                        'enable_time_tracking' => true,
                        'enable_kanban' => true,
                        'enable_gantt' => false
                    ],
                    'limits' => [
                        'projects' => 5,
                        'tasks_per_project' => 100
                    ]
                ],
                'sort_order' => 1
            ],
            [
                'key' => 'crm',
                'label' => 'CRM Light',
                'description' => 'Customer relationship management',
                'icon' => 'UserGroupIcon',
                'defaults' => [
                    'settings' => [
                        'enable_leads' => true,
                        'enable_deals' => true,
                        'enable_contacts' => true
                    ],
                    'limits' => [
                        'contacts' => 100,
                        'leads' => 50,
                        'deals' => 25
                    ]
                ],
                'sort_order' => 2
            ],
            [
                'key' => 'invoicing',
                'label' => 'Invoicing & Expenses',
                'description' => 'Create invoices and track expenses',
                'icon' => 'CurrencyDollarIcon',
                'defaults' => [
                    'settings' => [
                        'enable_recurring_invoices' => true,
                        'enable_expense_tracking' => true,
                        'enable_tax_calculation' => false
                    ],
                    'limits' => [
                        'invoices_per_month' => 10,
                        'expenses_per_month' => 50
                    ]
                ],
                'sort_order' => 3
            ],
            [
                'key' => 'marketing',
                'label' => 'Marketing Studio',
                'description' => 'Email marketing and campaign management',
                'icon' => 'MegaphoneIcon',
                'defaults' => [
                    'settings' => [
                        'enable_email_campaigns' => true,
                        'enable_landing_pages' => true,
                        'enable_analytics' => true
                    ],
                    'limits' => [
                        'email_contacts' => 1000,
                        'campaigns_per_month' => 5
                    ]
                ],
                'sort_order' => 4
            ],
            [
                'key' => 'automation',
                'label' => 'Automation',
                'description' => 'Workflow automation and integrations',
                'icon' => 'CogIcon',
                'defaults' => [
                    'settings' => [
                        'enable_webhooks' => true,
                        'enable_zapier' => true,
                        'enable_api_access' => true
                    ],
                    'limits' => [
                        'automations' => 10,
                        'api_calls_per_month' => 10000
                    ]
                ],
                'sort_order' => 5
            ],
            [
                'key' => 'analytics',
                'label' => 'Analytics Hub',
                'description' => 'Advanced analytics and reporting',
                'icon' => 'ChartBarIcon',
                'defaults' => [
                    'settings' => [
                        'enable_custom_reports' => true,
                        'enable_data_export' => true,
                        'enable_real_time_analytics' => true
                    ],
                    'limits' => [
                        'reports' => 20,
                        'data_retention_days' => 365
                    ]
                ],
                'sort_order' => 6
            ],
            [
                'key' => 'docs',
                'label' => 'Docs & Wiki',
                'description' => 'Documentation and knowledge base',
                'icon' => 'DocumentTextIcon',
                'defaults' => [
                    'settings' => [
                        'enable_versioning' => true,
                        'enable_collaboration' => true,
                        'enable_search' => true
                    ],
                    'limits' => [
                        'documents' => 100,
                        'storage_mb' => 1000
                    ]
                ],
                'sort_order' => 7
            ],
            [
                'key' => 'helpdesk',
                'label' => 'Helpdesk',
                'description' => 'Customer support and ticket management',
                'icon' => 'LifeRingIcon',
                'defaults' => [
                    'settings' => [
                        'enable_ticket_automation' => true,
                        'enable_knowledge_base' => true,
                        'enable_satisfaction_surveys' => true
                    ],
                    'limits' => [
                        'tickets_per_month' => 500,
                        'agents' => 10
                    ]
                ],
                'sort_order' => 8
            ]
        ];

        foreach ($modules as $module) {
            DB::table('modules')->updateOrInsert(
                ['key' => $module['key']],
                $module
            );
        }
    }

    private function seedDemoTenant(): void
    {
        // Create demo tenant
        $tenant = Tenant::create([
            'id' => Str::uuid(),
            'name' => 'Demo Company',
            'domain' => 'demo',
            'plan' => 'team',
            'is_active' => true,
            'trial_ends_at' => now()->addDays(30),
        ]);

        // Create demo user
        $user = User::create([
            'id' => Str::uuid(),
            'name' => 'Demo User',
            'email' => 'demo@atlas.com',
            'password' => Hash::make('password'),
            'tenant_id' => $tenant->id,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Assign owner role
        $user->assignRole('owner');

        // Enable modules for demo tenant
        $modules = Module::whereIn('key', ['tasks', 'crm', 'invoicing', 'docs'])->get();
        
        foreach ($modules as $module) {
            $tenant->modules()->attach($module->id, [
                'enabled' => true,
                'limits' => $module->defaults['limits'] ?? [],
                'settings' => $module->defaults['settings'] ?? []
            ]);
        }

        $this->command->info('Demo tenant created with email: demo@atlas.com and password: password');
    }
}
