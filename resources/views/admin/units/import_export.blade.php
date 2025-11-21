@extends('layouts.admin.app')
@section('title', translate('messages.Units Import/Export'))
@section('content')

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">{{ translate('messages.Units Import/Export') }}</h1>
    <p class="mb-4">{{ translate('messages.Import new units or update existing units using Excel files.') }}</p>

    <div class="row">
        <!-- Import New Units Section -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-plus-circle"></i> {{ translate('messages.Import New Units') }}
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        {{ translate('messages.Upload an Excel file to add new units to the system.') }}
                    </p>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>{{ translate('messages.How it works:') }}</strong>
                        <ul class="mb-0 mt-2">
                            <li>{{ translate('messages.Download the template below') }}</li>
                            <li>{{ translate('messages.Fill in unit details (leave ID column empty)') }}</li>
                            <li>{{ translate('messages.Upload the completed file') }}</li>
                            <li>{{ translate('messages.New units will be created automatically') }}</li>
                        </ul>
                    </div>

                    <!-- Download Template Button -->
                    <div class="text-center mb-3">
                        <a href="{{ route('admin.units.export') }}" class="btn btn-outline-success">
                            <i class="fas fa-download"></i>
                            {{ translate('messages.Download Template') }}
                        </a>
                    </div>

                    <hr>

                    <!-- Upload Form -->
                    <form action="{{ route('admin.units.import') }}" method="POST" enctype="multipart/form-data" id="importNewForm">
                        @csrf
                        <input type="hidden" name="import_type" value="new">
                        
                        <div class="form-group">
                            <label for="file_new">{{ translate('messages.Select Excel File') }}</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file_new" name="file" accept=".xlsx,.xls,.csv" required>
                                <label class="custom-file-label" for="file_new">{{ translate('messages.Choose file...') }}</label>
                            </div>
                            <small class="form-text text-muted">
                                {{ translate('messages.Supported formats: XLSX, XLS, CSV (Max 10MB)') }}
                            </small>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>{{ translate('messages.Important:') }}</strong>
                            <ul class="mb-0 mt-2 small">
                                <li>{{ translate('messages.Leave ID column empty for new units') }}</li>
                                <li>{{ translate('messages.Required fields: Name') }}</li>
                            </ul>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-upload fa-lg"></i>
                                {{ translate('messages.Import New Units') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Existing Units Section -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-edit"></i> {{ translate('messages.Update Existing Units') }}
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        {{ translate('messages.Upload an Excel file to update existing units in bulk.') }}
                    </p>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>{{ translate('messages.How it works:') }}</strong>
                        <ul class="mb-0 mt-2">
                            <li>{{ translate('messages.Export current units to get their IDs') }}</li>
                            <li>{{ translate('messages.Modify the data you want to update') }}</li>
                            <li>{{ translate('messages.Keep the ID column filled') }}</li>
                            <li>{{ translate('messages.Upload the file to update units') }}</li>
                        </ul>
                    </div>

                    <!-- Export Current Units Button -->
                    <div class="text-center mb-3">
                        <a href="{{ route('admin.units.export') }}" class="btn btn-outline-success">
                            <i class="fas fa-file-excel"></i>
                            {{ translate('messages.Export Current Units') }}
                        </a>
                    </div>

                    <hr>

                    <!-- Upload Form -->
                    <form action="{{ route('admin.units.import') }}" method="POST" enctype="multipart/form-data" id="importUpdateForm">
                        @csrf
                        <input type="hidden" name="import_type" value="update">
                        
                        <div class="form-group">
                            <label for="file_update">{{ translate('messages.Select Excel File') }}</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file_update" name="file" accept=".xlsx,.xls,.csv" required>
                                <label class="custom-file-label" for="file_update">{{ translate('messages.Choose file...') }}</label>
                            </div>
                            <small class="form-text text-muted">
                                {{ translate('messages.Supported formats: XLSX, XLS, CSV (Max 10MB)') }}
                            </small>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>{{ translate('messages.Important:') }}</strong>
                            <ul class="mb-0 mt-2 small">
                                <li>{{ translate('messages.ID column must be filled with existing unit IDs') }}</li>
                                <li>{{ translate('messages.Only units with valid IDs will be updated') }}</li>
                            </ul>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-sync-alt fa-lg"></i>
                                {{ translate('messages.Update Units') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructions Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-question-circle"></i> {{ translate('messages.Excel Template Format') }}
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ translate('messages.Column Name') }}</th>
                            <th>{{ translate('messages.Description') }}</th>
                            <th>{{ translate('messages.Required') }}</th>
                            <th>{{ translate('messages.Example') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>ID</code></td>
                            <td>{{ translate('messages.Unit ID (leave empty for new units)') }}</td>
                            <td><span class="badge badge-secondary">{{ translate('messages.Optional') }}</span></td>
                            <td><code>1</code></td>
                        </tr>
                        <tr>
                            <td><code>Name</code></td>
                            <td>{{ translate('messages.Unit name') }}</td>
                            <td><span class="badge badge-danger">{{ translate('messages.Required') }}</span></td>
                            <td><code>Kilogram</code></td>
                        </tr>
                        <tr>
                            <td><code>Symbol</code></td>
                            <td>{{ translate('messages.Unit symbol') }}</td>
                            <td><span class="badge badge-secondary">{{ translate('messages.Optional') }}</span></td>
                            <td><code>kg</code></td>
                        </tr>
                        <tr>
                            <td><code>Type</code></td>
                            <td>{{ translate('messages.Unit type (weight, volume, length, count, other)') }}</td>
                            <td><span class="badge badge-secondary">{{ translate('messages.Optional') }}</span></td>
                            <td><code>weight</code></td>
                        </tr>
                        <tr>
                            <td><code>Status</code></td>
                            <td>{{ translate('messages.Active or Inactive') }}</td>
                            <td><span class="badge badge-secondary">{{ translate('messages.Optional') }}</span></td>
                            <td><code>Active</code></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Update file input label with selected filename for both forms
        $('.custom-file-input').on('change', function() {
            const fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });

        // Form validation for new units
        $('#importNewForm').on('submit', function(e) {
            const fileInput = $('#file_new');
            if (!fileInput.val()) {
                e.preventDefault();
                toastr.error("{{ translate('messages.Please select a file to import') }}");
                return false;
            }
        });

        // Form validation for update units
        $('#importUpdateForm').on('submit', function(e) {
            const fileInput = $('#file_update');
            if (!fileInput.val()) {
                e.preventDefault();
                toastr.error("{{ translate('messages.Please select a file to import') }}");
                return false;
            }
        });
    });
</script>
@endpush
