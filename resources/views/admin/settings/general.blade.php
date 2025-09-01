@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-lg-8">
			<div class="card">
				<div class="card-header">General Settings</div>
				<div class="card-body">
					<form method="POST" action="{{ route('admin.settings.general.update') }}" enctype="multipart/form-data">
						@csrf
						<div class="mb-3">
							<label class="form-label">Site Name</label>
							<input type="text" class="form-control" name="site_name" value="{{ $settings['site_name'] ?? '' }}">
						</div>

						<div class="mb-3">
							<label class="form-label">Admin URL Prefix</label>
							<input type="text" class="form-control" name="admin_prefix" value="{{ $settings['admin_prefix'] ?? config('app.admin_prefix', 'admi') }}" placeholder="e.g. admi">
							<small class="text-muted">Changing this updates the admin login URL. Example: https://your-site.com/{prefix}</small>
						</div>

						<div class="mb-3">
							<label class="form-label">Logo</label>
							<input type="file" class="form-control" name="site_logo">
						</div>

						<button class="btn btn-primary" type="submit">Save Settings</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

