<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 bg-gradient-danger text-white rounded-top">
                <div class="d-flex align-items-center">
                    <i class="fas fa-sign-out-alt fa-lg mr-3"></i>
                    <h5 class="modal-title" id="logoutModalLabel">{{ translate('messages.Ready to Leave?') }}</h5>
                </div>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-4 px-4">
                <div class="text-center mb-3">
                    <i class="fas fa-question-circle fa-3x text-warning mb-3"></i>
                </div>
                <p class="text-gray-700 text-center mb-0">
                    {{ translate('messages.Are you sure you want to logout?') }}
                </p>
                <p class="text-gray-600 text-center small mt-2">
                    {{ translate('messages.You will need to login again to access the admin panel.') }}
                </p>
            </div>
            <div class="modal-footer border-0 bg-light d-flex justify-content-center gap-3 py-3">
                <button class="btn btn-secondary btn-sm px-4" type="button" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>{{ translate('messages.Cancel') }}
                </button>
                <form method="POST" action="{{ route('admin.auth.logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm px-4">
                        <i class="fas fa-sign-out-alt mr-2"></i>{{ translate('messages.Logout') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-danger {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    }
    
    .modal-content {
        border-radius: 0.5rem;
    }
    
    .modal-header {
        border-radius: 0.5rem 0.5rem 0 0;
    }
    
    #logoutModal .modal-dialog {
        max-width: 400px;
    }
    
    #logoutModal .btn {
        border-radius: 0.25rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    #logoutModal .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    #logoutModal .text-gray-700 {
        color: #2e3338;
        font-size: 0.95rem;
        line-height: 1.5;
    }
    
    #logoutModal .text-gray-600 {
        color: #6c757d;
    }
</style>