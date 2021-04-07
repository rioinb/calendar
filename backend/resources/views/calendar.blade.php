@extends('layouts.app')

@section('content')
<div class="container">
    <calendar :user = {{ Auth::user() }}></calendar>
</div>

{{-- <script>
    const userId = {{ auth()->user()->id }};
    console.log(userId);
</script> --}}
@endsection
