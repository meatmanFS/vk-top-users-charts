@extends('layouts.dashboard')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Settings</div>

                <div class="panel-body">
                    @include('layouts.errors')
                    <form method="POST" action="{{ url('/dashboard/settings') }}">
                        {{ csrf_field() }}
                        @if( $access_token  )
                        <div class="alert alert-success" role="alert">
                            Authorization successeful! Access token recieved.
                        </div>
                        @endif
                        <div class="form-group">
                            <label for="client_id">Application ID</label>
                            <input type="text" class="form-control" id="client_id" name="client_id" value="{{ $client_id }}" required>
                        </div> 
                        <div class="form-group">
                            <label for="client_secret">Client Secret</label>
                            <input type="text" class="form-control" id="client_secret" name="client_secret" value="{{ $client_secret }}" required>
                        </div>                  
                        <div class="form-group">
                            <label for="vk_users_number">Number of importing users</label>
                            <input type="text" class="form-control" id="vk_users_number" name="vk_users_number" value="{{ $vk_users_number }}">
                        </div>                  
                        @if( $oauth_error )
                            <div class="form-group">
                                <div class="alert alert-danger">
                                    {{ $oauth_error }}
                                </div>
                            </div>
                        @endif     
                        <button type="submit" class="btn btn-primary">Save</button>
                        @if( $login_url  )
                        <a href="{{ $login_url }}" class="btn btn-success" role="button">Login</a>
                        @endif
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
