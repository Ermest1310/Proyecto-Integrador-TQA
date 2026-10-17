@if(\App\Services\PermissionService::can($permission))
    {{ $slot }}
@endif