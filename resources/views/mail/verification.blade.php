@extends('mail.template.master')

@section('content')
    Hi  {{$user->first_name}} ,<br>Your verification  code is  <b>{{$user->code}}</b><br>BR ,<br>Alquran
@endsection