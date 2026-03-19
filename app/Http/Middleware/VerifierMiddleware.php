<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class VerifierMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {        
        if (Auth::user()->hasPermissionTo('admin_roles_permissions')) {
            return $next($request);
        }
        if ($request->is('admin/customers/create')) {
            if (!Auth::user()->hasPermissionTo('customers_create')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/customers/delete/*')) {
            if (!Auth::user()->hasPermissionTo('customers_delete')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/customers/edit/*')) {
            if (!Auth::user()->hasPermissionTo('customers_edit')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/customers/export/*')) {
            if (!Auth::user()->hasPermissionTo('customers_export')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/customers/import/*')) {
            if (!Auth::user()->hasPermissionTo('customers_import')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/customers')) {
            if (!Auth::user()->hasPermissionTo('customers_view')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/invoices/create')) {
            if (!Auth::user()->hasPermissionTo('invoices_create')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/invoices/delete/*')) {
            if (!Auth::user()->hasPermissionTo('invoices_delete')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/invoices/edit/*')) {
            if (!Auth::user()->hasPermissionTo('invoices_edit')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/invoices/export/*')) {
            if (!Auth::user()->hasPermissionTo('invoices_export')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/invoices/import/*')) {
            if (!Auth::user()->hasPermissionTo('invoices_import')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/invoices')) {
            if (!Auth::user()->hasPermissionTo('invoices_view')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/posts/create')) {
            if (!Auth::user()->hasPermissionTo('posts_create')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/posts/delete/*')) {
            if (!Auth::user()->hasPermissionTo('posts_delete')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/posts/edit/*')) {
            if (!Auth::user()->hasPermissionTo('posts_edit')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/posts/export/*')) {
            if (!Auth::user()->hasPermissionTo('posts_export')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/posts/import/*')) {
            if (!Auth::user()->hasPermissionTo('posts_import')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/posts')) {
            if (!Auth::user()->hasPermissionTo('posts_view')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/sales/create')) {
            if (!Auth::user()->hasPermissionTo('sales_create')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/sales/delete/*')) {
            if (!Auth::user()->hasPermissionTo('sales_delete')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/sales/edit/*')) {
            if (!Auth::user()->hasPermissionTo('sales_edit')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/sales/export/*')) {
            if (!Auth::user()->hasPermissionTo('sales_export')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/sales/import/*')) {
            if (!Auth::user()->hasPermissionTo('sales_import')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/sales')) {
            if (!Auth::user()->hasPermissionTo('sales_view')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/settings/create')) {
            if (!Auth::user()->hasPermissionTo('settings_create')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/settings/delete/*')) {
            if (!Auth::user()->hasPermissionTo('settings_delete')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/settings/edit/*')) {
            if (!Auth::user()->hasPermissionTo('settings_edit')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/settings/export/*')) {
            if (!Auth::user()->hasPermissionTo('settings_export')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/settings/import/*')) {
            if (!Auth::user()->hasPermissionTo('settings_import')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('admin/settings')) {
            if (!Auth::user()->hasPermissionTo('settings_view')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        return $next($request);
    }
}
