<?php

namespace App\Exports;

use App\Services\UserManagementService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $userManagementService;
    protected $userType;
    protected $filters;

    public function __construct($userType = 'user', $filters = [])
    {
        $this->userManagementService = app(UserManagementService::class);
        $this->userType = $userType;
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->userManagementService->getAllUsersForExport($this->userType, $this->filters);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Username',
            'Email',
            'Phone',
            'User Type',
            'Status',
            'Email Verified',
            'Joined Date',
        ];
    }

    /**
     * @param mixed $user
     * @return array
     */
    public function map($user): array
    {
        return [
            $user->id,
            $user->first_name . ' ' . $user->last_name,
            $user->username ?? 'N/A',
            $user->email,
            $user->phone ?? 'N/A',
            ucfirst($user->user_type),
            $user->is_active == 0 ? 'Active' : ($user->is_active == 1 ? 'Pending' : 'Unknown'),
            $user->email_verified_at ? 'Yes' : 'No',
            $user->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
