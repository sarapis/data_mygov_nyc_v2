@extends('layouts.app')
@section('title', 'Agencies and Departments')

@section('content')

<div class="demo-container mdl-grid inner_organization">
    <div class="demo-content mdl-color--white mdl-shadow--4dp content mdl-color-text--grey-800 mdl-cell mdl-cell--12-col">
        <div class="page-title row ">
            <div class="col-sm-2 image_main"> 
              @if($organization->logo!='')
                  <img src="{{$organization->logo}}" class="img-responsive center" >
              @endif
            </div>
            <div class="col-sm-10 col-xs-12">
                <div class="pull-right">
                    <p class="text-tages"> Tags: <?php 
                        $tag_names = explode(',', $organization->tag_names);
                    ?>
                        @foreach($tag_names as $tag_name)
                            @if($tag_name!='')
                            <span class="badge bg-blue">{{$tag_name}}</span>
                            @endif
                        @endforeach
                    </p>
                </div>
                <div class="pull-right" style="padding-left: 20px;padding-right: 10px;">
                    <p class="text-types"> Type: 
                        <span class="badge bg-blue">{{$organization_type}}</span>
                    </p>
                </div>
                <div class="pull-left org_title">{{$organization->name}}</div>
                <div class="social_icon">
                    <ul>
                        <li><a href="#" title="Website"><i class="fas fa-globe"></i></a></li>
                        <li><a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="#" title="Twitter"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="#" title="RSS"><i class="fas fa-rss"></i></a></li>
                        <li><a href="#" title="Phone"><i class="fas fa-phone"></i></a></li>
                        <li><a href="#" title="Email"><i class="fas fa-envelope"></i></a></li>
                        <li><a href="#" title="Address"><i class="fas fa-map-marker-alt"></i></a></li>
                    </ul>
                </div>
                <input type="hidden" id="organizations_id" value="{{$organization->organizations_id}}">
            </div>
        </div>
        <div class="menu-bar row">
            <ul class="nav nav-tabs desktop_tab" role="tablist">
                <li><a href="/organization_{{$organization->organizations_id}}" class="menu-title">ABOUT</a></li>
                <li><a href="/organization_{{$organization->organizations_id}}/projects" id="projects_tab">PROJECTS</a></li>
                <li><a href="/organization_{{$organization->organizations_id}}/services" id="services_tab">SERVICES</a></li>
                <li><a href="/organization_{{$organization->organizations_id}}/money" id="money_tab">MONEY</a></li>
                <li><a href="/organization_{{$organization->organizations_id}}/people" id="peoples_tab">PEOPLE</a></li>
                <li><a href="/organization_{{$organization->organizations_id}}/laws" id="laws_tab">CHARTER, CODE & RULES</a></li>
                <li><a href="/organization_{{$organization->organizations_id}}/endorsements" id="laws_tab">ENDORSEMENTS</a></li>
                <li><a href="/organization_{{$organization->organizations_id}}/candidates" id="legislation_tab">CANDIDATES</a></li>
                <li  class="active"><a href="/organization_{{$organization->organizations_id}}/requests" id="requests_tab">REQUESTS</a></li>
            </ul>

            <button type="button" class="btn btn-raised btn-block btn-primary org_filter">Organization Filter </button>

            <div class="tab_filter_btn">
                <ul class="nav nav-tabs" role="tablist">
                    <li><a href="/organization_{{$organization->organizations_id}}" class="menu-title">ABOUT</a></li>
                    <li><a href="/organization_{{$organization->organizations_id}}/projects" id="projects_tab">PROJECTS</a></li>
                    <li><a href="/organization_{{$organization->organizations_id}}/services" id="services_tab">SERVICES</a></li>
                    <li><a href="/organization_{{$organization->organizations_id}}/money" id="money_tab">MONEY</a></li>
                    <li><a href="/organization_{{$organization->organizations_id}}/people" id="peoples_tab">PEOPLE</a></li>
                    <li><a href="/organization_{{$organization->organizations_id}}/laws" id="laws_tab">CHARTER, CODE & RULES</a></li>
                    <li><a href="/organization_{{$organization->organizations_id}}/endosements" id="endosements_tab">ENDORSEMENTS</a></li>
                    <li><a href="/organization_{{$organization->organizations_id}}/candidates" id="candidates_tab">CANDIDATES</a></li>
                    <li  class="active"><a href="/organization_{{$organization->organizations_id}}/requests" id="requests_tab">REQUESTS</a></li>
                </ul>
            </div>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="requests_tab">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="box padding_0 table_data">
                                <div class="padding_0 endosment_table table-responsive">
                                    <table id="example3" class="table table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center;">Tracking Code</th>
                                                <th style="text-align: center;">Borough</th>
                                                <th style="text-align: center;">Community District</th>
                                                <th style="text-align: center;">Priority Number</th>
                                                <th style="text-align: center;">Request</th>
                                                <th style="text-align: center;">Responsible Agency</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><a href="/organization_{{$organization->organizations_id}}/requests_details">101201901C</a></td>
                                                <td>Bronx</td>
                                                <td>Community District 1</td>
                                                <td>01</td>
                                                <td style="text-align: left;">Brooklyn Borough President</td>
                                                <td style="text-align: left;"><a href="#">Department of Environment</a></td>
                                            </tr>
                                            <tr>
                                                <td><a href="/organization_{{$organization->organizations_id}}/requests_details">101201902C</a></td>
                                                <td>Bronx</td>
                                                <td>Community District 2</td>
                                                <td>02</td>
                                                <td style="text-align: left;">Brooklyn Borough President</td>
                                                <td style="text-align: left;"><a href="#">Department of Environment</a></td>
                                            </tr>
                                            <tr>
                                                <td><a href="/organization_{{$organization->organizations_id}}/requests_details">101201903C</a></td>
                                                <td>Bronx</td>
                                                <td>Community District 3</td>
                                                <td>03</td>
                                                <td style="text-align: left;">Brooklyn Borough President</td>
                                                <td style="text-align: left;"><a href="#">Department of Environment</a></td>
                                            </tr>
                                            <tr>
                                                <td><a href="/organization_{{$organization->organizations_id}}/requests_details">101201904C</a></td>
                                                <td>Bronx</td>
                                                <td>Community District 4</td>
                                                <td>04</td>
                                                <td style="text-align: left;">Brooklyn Borough President</td>
                                                <td style="text-align: left;"><a href="#">Department of Environment</a></td>
                                            </tr>
                                            <tr>
                                                <td><a href="/organization_{{$organization->organizations_id}}/requests_details">101201905C</a></td>
                                                <td>Bronx</td>
                                                <td>Community District 5</td>
                                                <td>05</td>
                                                <td style="text-align: left;">Brooklyn Borough President</td>
                                                <td style="text-align: left;"><a href="#">Department of Environment</a></td>
                                            </tr>
                                            <tr>
                                                <td><a href="/organization_{{$organization->organizations_id}}/requests_details">101201906C</a></td>
                                                <td>Bronx</td>
                                                <td>Community District 6</td>
                                                <td>06</td>
                                                <td style="text-align: left;">Brooklyn Borough President</td>
                                                <td style="text-align: left;"><a href="#">Department of Environment</a></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gmaps.js/0.4.24/gmaps.js"></script>

<script type="text/javascript">

    var locations = <?php print_r(json_encode($agency_map)) ?>;

    if (locations !== null) {
        var mymap = new GMaps({
          el: '#mymap',
          lat: locations.latitude,
          lng: locations.longitude,
          zoom:10
        });
    }
    else{
        var mymap = new GMaps({
          el: '#mymap',
          lat: 40.712722,
          lng: -74.006058,
          zoom:10
        });
    }

    if (locations !== null) {
        mymap.addMarker({
            lat: locations.latitude,
            lng: locations.longitude,
            title: locations.name,
            infoWindow: {
            content: ('<span>' +locations.address_1+', ' +locations.city+', '+locations.state_province+', '+locations.postal_code+'</span>')
            }
        });
    }

  setTimeout(function(){var iframe = $('#newsiframe');
  $('.rss2html-note',iframe.contents()).hide();},5000);

    

</script>


@include('layouts.script')
@endsection
