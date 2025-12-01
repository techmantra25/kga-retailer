@extends('layouts.app')
@section('content')
@section('page', 'Show Warranty')
<section> 
    @if (Session::has('message'))
    <div class="alert alert-success" role="alert">
        {{ Session::get('message') }}
        {{ Session::forget('message') }}
    </div>
    @endif  
    <ul class="breadcrumb_menu">
        <li>Product</li>
        <li>{{$product->title}}</li>
        <li>Show Warranty</li>
    </ul>
    <ul class="breadcrumb_menu">
        <li>
            <a href="{{ route('product.list') }}?{{$getQueryString}}">
                <i class="fi-br-arrow-alt-circle-left"></i>
                Back To Product
            </a>
        </li>      
        <li>
            <a href="{{ route('product.add-goods-warranty',  [Crypt::encrypt($product->id),Request::getQueryString()] ) }}" class="btn btn-outline-primary select-md">Add Warranty</a>
        </li>         
    </ul>
  
      <div class="row">
        @if (count($goods_warranty_khosla)>0)
          
        <div class="col-sm-6">
          <div class="card shadow-sm">
              <div class="card shadow-sm">
                  <div class="card-body">
                      <div class="form-group mb-3 d-flex justify-content-between">
                          <p>
                              <span class="badge bg-secondary">Dealer Type:- Khosla</span>
                          </p>
                          <form method="post" action="{{ route('product.duplicate-warranty') }}">
                            @csrf
                            <input type="hidden" name="dealer_type" value="khosla"/>
                            <input type="hidden" name="goods_id" value="{{$id}}"/>
                            <button type="submit" class="btn btn-outline-primary select-md">Duplicate as khosla</button>
                          </form>
                      </div> 
                      {{-- <div class="form-group mb-3">
                          <a href="{{ route('product.remove-goods-warranty', [$idStr,$goods_warranty_khosla->dealer_type,$getQueryString]) }}" class="btn btn-outline-danger" onclick="return confirm('Are you sure?');">Remove</a>
                      </div>  --}}
                      
                      <table class="table">
                          <thead>
                            <tr>
                                <th>Warranty Type</th>
                                <th>Spear Parts</th>
                                <th>Warranty Period (In Month)</th>
                                <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach ($goods_warranty_khosla as $kitem)
                            <tr>
                                <td>
									<!--Display the formatted warranty type -->
									@if($kitem->warranty_type == 'cleaning')
										{{ 'Normal Cleaning' }}
									@elseif($kitem->warranty_type == 'deep_cleaning')
										{{ 'Deep Cleaning' }}
									@else
										{{ ucwords($kitem->warranty_type) }}
									@endif
									<!--Display badge for number_of_cleaning-->
                                    @if($kitem->warranty_type=="cleaning" && $kitem->number_of_cleaning>0)
                                        <p class="badge bg-danger" style="cursor: pointer; font-size: 9px !important;" title="Number Of Cleaning">{{$kitem->number_of_cleaning}}</p>
                                    @endif
									@if($kitem->warranty_type=="deep_cleaning" && $kitem->number_of_deep_cleaning>0)
									     <p class="badge bg-danger" style="cursor: pointer; font-size: 9px !important;" title="Number Of Cleaning">{{$kitem->number_of_deep_cleaning}}</p>
									@endif
                                    <br>
                                    @if($kitem->warranty_type=="additional" && $kitem->additional_warranty_type==1)
                                        <p class="badge bg-danger" style="cursor: pointer; font-size: 9px !important;">Parts Chargeable</p>
                                    @endif
                                    @if($kitem->warranty_type=="additional" && $kitem->additional_warranty_type==2)
                                        <p class="badge bg-danger" style="cursor: pointer; font-size: 9px !important;">Service Chargeable</p>
                                    @endif
                                    
                                </td>
                              <td><span class="badge bg-success">{{$kitem->spear_goods?$kitem->spear_goods->title:""}}</span></td>
                              <td><span class="badge bg-success">{{$kitem->warranty_period}}</span></td>
                              <td><a href="{{ route('product.remove-goods-warranty', $kitem->id) }}" class="badge bg-danger text-decoration-none"><i class="fi-br-trash"></i></a></td>
                            </tr>
                            @endforeach
                          </tbody>
                      </table>
                  </div>
              </div>  
          </div>                                      
        </div>
        @endif
        @if (count($goods_warranty_nonkhosla)>0)
          <div class="col-sm-6">
            <div class="card shadow-sm">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="form-group mb-3 d-flex justify-content-between">
                            <p>
                                <span class="badge bg-secondary">Dealer Type:-Non-Khosla</span>
                            </p>
                            <form method="post" action="{{ route('product.duplicate-warranty') }}">
                                @csrf
                                <input type="hidden" name="dealer_type" value="nonkhosla"/>
                                <input type="hidden" name="goods_id" value="{{$id}}"/>
                                <button type="submit" class="btn btn-outline-primary select-md">Duplicate as non-khosla</button>
                            </form>
                        </div> 
                        <table class="table">
                            <thead>
                              <tr>
                                  <th>Warranty Type</th>
                                  <th>Spear Parts</th>
                                  <th>Warranty Period (In Month)</th>
                                  <th>Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach ($goods_warranty_nonkhosla as $Nkitem)
                              <tr>
                                <td>
									@if($Nkitem->warranty_type == 'cleaning')
										{{ 'Normal Cleaning' }}
									@elseif($Nkitem->warranty_type == 'deep_cleaning')
										{{ 'Deep Cleaning' }}
									@else
										{{ ucwords($Nkitem->warranty_type) }}
									@endif
                                    @if($Nkitem->warranty_type=="cleaning" && $Nkitem->number_of_cleaning>0)
                                        <p class="badge bg-danger" style="cursor: pointer; font-size: 9px !important;" title="Number Of Cleaning">{{$Nkitem->number_of_cleaning}}</p>
                                    @endif
									@if($Nkitem->warranty_type=="deep_cleaning" && $Nkitem->number_of_deep_cleaning>0)
                                        <p class="badge bg-danger" style="cursor: pointer; font-size: 9px !important;" title="Number Of Cleaning">{{$Nkitem->number_of_deep_cleaning}}</p>
                                    @endif
                                    <br>
                                    @if($Nkitem->warranty_type=="additional" && $Nkitem->additional_warranty_type==1)
                                        <p class="badge bg-danger" style="cursor: pointer; font-size: 9px !important;">Parts Chargeable</p>
                                    @endif
                                    @if($Nkitem->warranty_type=="additional" && $Nkitem->additional_warranty_type==2)
                                        <p class="badge bg-danger" style="cursor: pointer; font-size: 9px !important;">Service Chargeable</p>
                                    @endif
                                    
                                </td>
                                <td><span class="badge bg-success">{{$Nkitem->spear_goods?$Nkitem->spear_goods->title:""}}</span></td>
                                <td><span class="badge bg-success">{{$Nkitem->warranty_period}}</span></td>
                                <td><a href="{{ route('product.remove-goods-warranty', $Nkitem->id) }}" class="badge bg-danger text-decoration-none"><i class="fi-br-trash"></i></a></td>
                              </tr>
                              @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>  
            </div>                                      
          </div>
        @endif
      </div>
</section>
<script>
    
    $(document).ready(function(){  
       
    });

   

    $("#myForm").submit(function() {
        $('#submitBtn').attr('disabled', 'disabled');
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>').append('   Please wait ...');
        
        return true;
    });

   
</script>
@endsection