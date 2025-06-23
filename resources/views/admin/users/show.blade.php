@extends('layouts.admin.app')
@section('title', translate('messages.user_profile'))

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ translate('messages.user_profile') }}</h1>
        <div>
            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-edit"></i> {{ translate('messages.edit') }}
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ translate('messages.back') }}
            </a>
        </div>
    </div>

    <div class="row">
        <!-- User Profile Card -->
        <div class="col-xl-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.profile_information') }}</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if($user->image_path)
                            <img src="{{ asset('storage/app/public/users/' . $user->image_path) }}" alt="{{ $user->first_name }}" class="img-profile rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <img src="{{ asset('assets/admin/img/undraw_profile.svg') }}" alt="{{ $user->first_name }}" class="img-profile rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        @endif
                    </div>
                    <h4 class="font-weight-bold">{{ $user->first_name }} {{ $user->last_name }}</h4>
                    <p class="text-muted mb-1">{{'@'.$user->username }}</p>
                    <p class="mb-2">
                        <span class="badge badge-{{ $user->role == 'user' ? 'primary' : ($user->role == 'volunteer' ? 'success' : 'warning') }} px-3 py-2">
                            {{ ucfirst($user->role) }}
                        </span>
                    </p>
                    <p class="mb-2">
                        <span class="badge badge-{{ $user->status == 'active' ? 'success' : ($user->status == 'pending' ? 'warning' : 'danger') }} px-3 py-2">
                            {{ ucfirst($user->status) }}
                        </span>
                    </p>
                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-envelope mr-2"></i> {{ translate('messages.email') }}:</span>
                            <span class="text-primary">{{ $user->email }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-phone mr-2"></i> {{ translate('messages.phone') }}:</span>
                            <span>{{ $user->phone }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-calendar mr-2"></i> {{ translate('messages.joined') }}:</span>
                            <span>{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                        @if($user->dob)
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-birthday-cake mr-2"></i> {{ translate('messages.dob') }}:</span>
                            <span>{{ \Carbon\Carbon::parse($user->dob)->format('M d, Y') }}</span>
                        </div>
                        @endif
                        @if($user->gender)
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-venus-mars mr-2"></i> {{ translate('messages.gender') }}:</span>
                            <span>{{ ucfirst($user->gender) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Verification Status Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.verification_status') }}</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>{{ translate('messages.email_verification') }}</span>
                        @if($user->email_verified_at)
                            <span class="badge badge-success">{{ translate('messages.verified') }}</span>
                        @else
                            <span class="badge badge-danger">{{ translate('messages.not_verified') }}</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>{{ translate('messages.account_status') }}</span>
                        @if($user->is_active)
                            <span class="badge badge-success">{{ translate('messages.active') }}</span>
                        @else
                            <span class="badge badge-danger">{{ translate('messages.inactive') }}</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>{{ translate('messages.newsletter_subscription') }}</span>
                        @if($user->subscribed_to_newsletter)
                            <span class="badge badge-success">{{ translate('messages.subscribed') }}</span>
                        @else
                            <span class="badge badge-secondary">{{ translate('messages.not_subscribed') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- User Details Card -->
        <div class="col-xl-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.user_details') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="font-weight-bold">{{ translate('messages.personal_information') }}</h6>
                                <hr>
                                <div class="row mb-2">
                                    <div class="col-sm-5 text-muted">{{ translate('messages.first_name') }}:</div>
                                    <div class="col-sm-7">{{ $user->first_name }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-5 text-muted">{{ translate('messages.last_name') }}:</div>
                                    <div class="col-sm-7">{{ $user->last_name }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-5 text-muted">{{ translate('messages.username') }}:</div>
                                    <div class="col-sm-7">{{ $user->username }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-5 text-muted">{{ translate('messages.email') }}:</div>
                                    <div class="col-sm-7">{{ $user->email }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-5 text-muted">{{ translate('messages.phone') }}:</div>
                                    <div class="col-sm-7">{{ $user->phone }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="font-weight-bold">{{ translate('messages.address_information') }}</h6>
                                <hr>
                                <div class="row mb-2">
                                    <div class="col-sm-5 text-muted">{{ translate('messages.address') }}:</div>
                                    <div class="col-sm-7">{{ $user->address ?? translate('messages.not_provided') }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-5 text-muted">{{ translate('messages.city') }}:</div>
                                    <div class="col-sm-7">{{ $user->city ?? translate('messages.not_provided') }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-5 text-muted">{{ translate('messages.division') }}:</div>
                                    <div class="col-sm-7">{{ $user->division ?? translate('messages.not_provided') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="font-weight-bold">{{ translate('messages.referral_information') }}</h6>
                                <hr>
                                <div class="row mb-2">
                                    <div class="col-sm-5 text-muted">{{ translate('messages.referral_code') }}:</div>
                                    <div class="col-sm-7">
                                        @if($user->referral_code)
                                            <span class="badge badge-light">{{ $user->referral_code }}</span>
                                        @else
                                            {{ translate('messages.not_available') }}
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-5 text-muted">{{ translate('messages.referred_by') }}:</div>
                                    <div class="col-sm-7">
                                        @if($user->referred_by)
                                            <span class="badge badge-light">{{ $user->referred_by }}</span>
                                        @else
                                            {{ translate('messages.not_referred') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="font-weight-bold">{{ translate('messages.account_information') }}</h6>
                                <hr>
                                <div class="row mb-2">
                                    <div class="col-sm-5 text-muted">{{ translate('messages.created_at') }}:</div>
                                    <div class="col-sm-7">{{ $user->created_at->format('M d, Y H:i:s') }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-5 text-muted">{{ translate('messages.updated_at') }}:</div>
                                    <div class="col-sm-7">{{ $user->updated_at->format('M d, Y H:i:s') }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-5 text-muted">{{ translate('messages.email_verified_at') }}:</div>
                                    <div class="col-sm-7">
                                        {{ $user->email_verified_at ? $user->email_verified_at->format('M d, Y H:i:s') : translate('messages.not_verified') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Activity Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.recent_activity') }}</h6>
                </div>
                <div class="card-body">
                    <p class="text-center text-muted">{{ translate('messages.activity_history_will_be_displayed_here') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 