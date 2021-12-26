@extends('layouts.backend.app')
@section('content')
    @include('layouts.backend.partials.headersection',['title'=>'Address Requests'])


    <div class="row">
        <div class="col-12 mt-2">
            <div class="card">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>


                                <th class="am-title">Cities</th>

                                <th class="am-tags">Requests</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($addresses as $address)
                                <tr>
                                    <td>
                                        {{ $address->city }}
                                    </td>
                                    @if (in_array(Str::lower($address->city), $cities))
                                        <td> Active, You have {{ $address->total}} save addresses from this city  </td>
                                    @else
                                        @if ($address->total >= 5)
                                        <td>You have $address->total addresses from this city! Please Active this city for delivery. </td>
                                        @else
                                        <td>{{ 5 - $address->total }}
                                                    Requests left to Active this city for delivery. </td>
                                        @endif
                                    @endif
                                    
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('admin/js/custom_check.js') }}"></script>

@endsection
