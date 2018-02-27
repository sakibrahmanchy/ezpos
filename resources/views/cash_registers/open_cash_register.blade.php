@extends('layouts.master')

@section('pageTitle','Open Cash Register')

@section('breadcrumbs')
    {{--{!! Breadcrumbs::render('new_sale') !!}--}}
    <span><label class="label label-primary pull-right counter-name"><b>{{ \Illuminate\Support\Facades\Cookie::get('counter_name') }}</b></label></span>
    <br><br>
    <a href="javascript:void(0)"  onclick="changeCounter()" class="pull-right">Change Location</a>
    <br>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-piluku">
                <div class="panel-heading">
                    Please enter an opening amount to get in the sales register			</div>
                <form action="{{route('open_cash_register')}}" id="opening_balance_form" method="post" accept-charset="utf-8" novalidate="novalidate">
                <div class="panel-body">

                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover text-center opening_bal">
                                <tbody><tr>
                                    <th>Denomination</th>
                                    <th>Count</th>
                                </tr>
                                @php $den_count = 0; @endphp
                                @foreach($denominations as $aDenomination)
                                    <tr>
                                        <td>{{$aDenomination->denomination_name}}</td>
                                        <td>
                                            <div class="form-group">
                                                <input type="text" name="denom.{{$aDenomination->id}}" value="" id="denomination_name[]" data-value="{{$aDenomination->denomination_value}}" class="form-control denomination">
                                            </div>
                                        </td>
                                    </tr>
                                    @php $den_count++; @endphp
                                @endforeach

                                </tbody></table>
                        </div>
                    </div>

                    <div class="col-md-6">


                            <input type="hidden" value = "{{$den_count}}" name="no_of_den">
                            <div class="form-group">

                                <div class="from-group text-center">
                                    Previous Closing Amount: ${{$previous_closing_balance}}						</div>

                                <label for="opening_balance" class="control-label col-sm-2">Opening amount:</label>                        <div class="col-sm-10">
                                    <div class="input-group">
                                        <input type="text" name="opening_balance" value="" id="opening_balance" class="form-control">
                                        <span class="input-group-btn bg">
   <input type="submit" name="submit" value="Save" id="submit" class="btn btn-primary">
   </span>
                                    </div>
                                    <span class="control-label col-sm-12 text-danger">{{ $errors->first('opening_balance') }}</span>
                                    <!-- /input-group -->
                                </div>
                            </div>


                            <div class="from-group text-center">
                                <h3>OR</h3>
                                Register Name: <a href="https://demo.phppointofsale.com/index.php/sales/clear_register">&nbsp;<span class="badge bg-primary">Default&nbsp;(<small>Change Register</small>)</span></a>					</div>
                            <br>
                            <div class="from-group text-right">
                                <a href="https://demo.phppointofsale.com/index.php/sales/open_drawer" onclick="window.open('https://demo.phppointofsale.com/index.php/sales/open_drawer', '_blank', 'width=800,height=600,scrollbars=yes,menubar=no,status=yes,resizable=yes,screenx=0,screeny=0'); return false;" class="" target="_blank"><i class="ion-android-open"></i> Pop Open Cash Drawer</a>					</div>
                        </form>				</div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="choose_counter_modal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="chooseCounter">Choose Counter</h4>
                </div>
                <div class="modal-body">
                    <ul class="list-inline choose-counter-home">

                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('additionalJS')
    <script>

        $(document).ready(function(){
            selectCounter();
        });
        function calculate_total()
        {
            var total = 0;

            $(".denomination").each(function( index )
            {
                if ($(this).val())
                {
                    total+= $(this).data('value') * $(this).val();
                }
            });

            $("#opening_balance").val(parseFloat(Math.round(total * 100) / 100).toFixed(2));
        }

        $(".denomination").change(calculate_total);
        $(".denomination").keyup(calculate_total);


        function selectCounter(){
            @if(is_null(\Illuminate\Support\Facades\Cookie::get('counter_id')))
            $("#choose_counter_modal").modal();
            $.ajax({
                url: "{{route('counter_list_ajax')}}",
                type:"get",
                dataType: "json",
                success: function(response){
                    $(".choose-counter-home").html("");
                    counters = response.counters;
                    counters.forEach(function(counter){
                        var url = '{{ route("counter_set", ":counter_id") }}';
                        url = url.replace(':counter_id', counter.id);
                        $(".choose-counter-home").append('<li><a class="set_employee_current_counter_after_login" href="'+url+'">'+counter.name+'</a></li>');
                    });
                },
                error: function () {

                }
            })
            @endif
        }


        function changeCounter(){

            $("#choose_counter_modal").modal();
            $.ajax({
                url: "{{route('counter_list_ajax')}}",
                type:"get",
                dataType: "json",
                success: function(response){
                    $(".choose-counter-home").html("");
                    counters = response.counters;
                    counters.forEach(function(counter){
                        var url = '{{ route("counter_set", ":counter_id") }}';
                        url = url.replace(':counter_id', counter.id);
                        $(".choose-counter-home").append('<li><a class="set_employee_current_counter_after_login" href="'+url+'">'+counter.name+'</a></li>');
                    });
                },
                error: function () {

                }
            })
        }


    </script>
@stop
