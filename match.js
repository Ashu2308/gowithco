
		// Google autocomplete script
        google.maps.event.addDomListener(window, 'load', function () {
			var places1 = new google.maps.places.Autocomplete(document.getElementById('txtPlaces1'));
			var places2 = new google.maps.places.Autocomplete(document.getElementById('txtPlaces2'));
			google.maps.event.addListener(places1, 'place_changed', function () {
				var place = places1.getPlace();
				var address = place.formatted_address;
				var latitude = place.geometry.location.lat();
				var longitude = place.geometry.location.lng();
				$("#txtPlaces1").attr("data-lat", latitude);
				$("#txtPlaces1").attr("data-lng", longitude);
				//getLatLng(places1)
                
            });
			google.maps.event.addListener(places2, 'place_changed', function () {
                var place = places2.getPlace();
				var address = place.formatted_address;
				var latitude = place.geometry.location.lat();
				var longitude = place.geometry.location.lng();
				$("#txtPlaces2").attr("data-lat", latitude);
				$("#txtPlaces2").attr("data-lng", longitude);
            });
        });
		
		// Get the midpoints of route and convert these points in address
        var directions = {};
        $(document).ready(function() {
            $("#search").click(function() {
                $("#location-address").html('');
				var OriLat = $("#start").find(':selected').attr('data-lat');
				var OriLng = $("#start").find(':selected').attr('data-lng');
				var desLat = $("#txtPlaces1").attr('data-lat');
				var desLng = $("#txtPlaces1").attr('data-lng');
				var OnLat = $("#txtPlaces2").attr('data-lat');
				var OnLng = $("#txtPlaces2").attr('data-lng');
                initMap(OriLat, OriLng, desLat, desLng, OnLat, OnLng);
				initMap1(OriLat, OriLng, OnLat, OnLng, desLat, desLng);
            });
        });

        function initMap(OriLat, OriLng, desLat, desLng, OnLat, OnLng) {
	    return OriLat;
	   
            var opts = {
                zoom: 5,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                center: new google.maps.LatLng(OriLat, OriLng)
            }

            map = new google.maps.Map(document.getElementById("map_canvas"), opts);

            var routes = [{
                label: 'Distance',
                request: {
                    origin: new google.maps.LatLng(OriLat, OriLng),
                    destination: new google.maps.LatLng(desLat, desLng),
                    travelMode: google.maps.DirectionsTravelMode.DRIVING
                },
                rendering: {
                    marker: {
                        icon: 'http://labs.google.com/ridefinder/images/mm_20_green.png'
                    },
                    draggable: true
                }
            }, ];
            var bounds = new google.maps.LatLngBounds();

            var dists = [1000];
            var selects = document.createElement('select');
            list = document.getElementsByTagName('ul')[0];
            for (var d = 0; d < dists.length; ++d) {
                selects.options[selects.options.length] = new Option(dists[d], dists[d], d == 0, d == 0);
            }

            for (var r = 0; r < routes.length; ++r) {
                bounds.extend(routes[r].request.destination);
                routes[r].rendering.routeId = 'r' + r + new Date().getTime();
                routes[r].rendering.dist = dists[0];
                var select = selects.cloneNode(true);
                select.setAttribute('name', routes[r].rendering.routeId);
                select.onchange = function() {
                    directions[this.name].renderer.dist = this.value;
                    //setMarkers(this.name)
                };
                list.appendChild(document.createElement('li'));
                list.lastChild.appendChild(select);
                list.lastChild.appendChild(document.createTextNode(routes[r].label));

                requestRoute(routes[r], map, desLat, desLng, OnLat, OnLng);
            }
            map.fitBounds(bounds);
        }
		//Another function for reverse mapping
		function initMap1(OriLat, OriLng, desLat, desLng, OnLat, OnLng) {
            var opts = {
                zoom: 5,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                center: new google.maps.LatLng(OriLat, OriLng)
            }

            map = new google.maps.Map(document.getElementById("map_canvas"), opts);
            var routes = [{
                label: 'Distance',
                request: {
                    origin: new google.maps.LatLng(OriLat, OriLng),
                    destination: new google.maps.LatLng(desLat, desLng),
                    travelMode: google.maps.DirectionsTravelMode.DRIVING
                },
                rendering: {
                    marker: {
                        icon: 'http://labs.google.com/ridefinder/images/mm_20_green.png'
                    },
                    draggable: true
                }
            }, ];
            var bounds = new google.maps.LatLngBounds();

            var dists = [1000];
            var selects = document.createElement('select');
            list = document.getElementsByTagName('ul')[0];
            for (var d = 0; d < dists.length; ++d) {
                selects.options[selects.options.length] = new Option(dists[d], dists[d], d == 0, d == 0);
            }

            for (var r = 0; r < routes.length; ++r) {
                bounds.extend(routes[r].request.destination);
                routes[r].rendering.routeId = 'r' + r + new Date().getTime();
                routes[r].rendering.dist = dists[0];
                var select = selects.cloneNode(true);
                select.setAttribute('name', routes[r].rendering.routeId);
                select.onchange = function() {
                    directions[this.name].renderer.dist = this.value;
                    //setMarkers(this.name)
                };
                list.appendChild(document.createElement('li'));
                list.lastChild.appendChild(select);
                list.lastChild.appendChild(document.createTextNode(routes[r].label));

                requestRoute(routes[r], map, desLat, desLng, OnLat, OnLng);
            }
            map.fitBounds(bounds);
        }


        function setMarkers(ID, desLat, desLng, OnLat, OnLng) {
		    var direction = directions[ID],
                renderer = direction.renderer,
                dist = renderer.dist,
                marker = renderer.marker,
                map = renderer.getMap(),
                dirs = direction.renderer.getDirections();
				marker.map = map;
            
            for (var k in direction.sets) {

                var set = directions[ID].sets[k];
                set.visible = !!(k === dist);
                for (var m = 0; m < set.length; ++m) {
                    set[m].setMap((set.visible) ? map : null);
                }
            }
            if (!direction.sets[dist]) {
                if (dirs.routes.length) {
                    var route = dirs.routes[0];
                    var az = 0;
                    for (var i = 0; i < route.legs.length; ++i) {
                        if (route.legs[i].distance) {
                            az += route.legs[i].distance.value;
                        }
                    }
                    dist = Math.max(dist, Math.round(az / 100));
                    direction.sets[dist] = gMilestone(route, dist, marker, desLat, desLng, OnLat, OnLng);
                }
            }
		}

        function requestRoute(route, map, desLat, desLng, OnLat, OnLng) {
		    if (!window.gDirSVC) {
                window.gDirSVC = new google.maps.DirectionsService();
            }
            var renderer = new google.maps.DirectionsRenderer(route.rendering);
            renderer.setMap(map);
            renderer.setOptions({
                preserveViewport: true
            })


            google.maps.event.addListener(renderer, 'directions_changed', function() {

                if (directions[this.routeId]) {
                    //remove markers
                    for (var k in directions[this.routeId].sets) {
                        for (var m = 0; m < directions[this.routeId].sets[k].length; ++m) {
                            directions[this.routeId].sets[k][m].setMap(null);
                        }
                    }
                }

                directions[this.routeId] = {
                    renderer: this,
                    sets: {}
                };
                setMarkers(this.routeId, desLat, desLng, OnLat, OnLng);
		    });

            window.gDirSVC.route(route.request, function(response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    renderer.setDirections(response);
                }
            });
        }
        /** 
         * creates markers along a google.maps.DirectionsRoute
         *
         * @param route object google.maps.DirectionsRoute
         * @param dist  int    interval for milestones in meters
         * @param opts  object google.maps.MarkerOptions
         * @return array Array populated with created google.maps.Marker-objects
         **/

        function gMilestone(route, dist, opts, desLat, desLng, OnLat, OnLng) {
		
			var dist = $("#dist").find(':selected').val();
			var desLat = parseFloat(desLat);
            var desLng = parseFloat(desLng);
			var OnLat = parseFloat(OnLat);
            var OnLng = parseFloat(OnLng);

            var markers = [],
                geo = google.maps.geometry.spherical,
                path = route.overview_path,
                point = path[0],
                distance = 0,
                leg,
                overflow,
                pos;
			var asd = 0;
			var diffLat = floorFigure(desLat) - floorFigure(OnLat);
			var LatDiff = (diffLat < 0) ? diffLat * -1 : diffLat;
			var diffLng = floorFigure(desLng) - floorFigure(OnLng);
			var LngDiff = (diffLng < 0) ? diffLng * -1 : diffLng;
			if(((floorFigure(OnLat) == floorFigure(desLat)) && (floorFigure(OnLng) == floorFigure(desLng))) || ((LatDiff <= 0.02) && (LngDiff <= 0.02))){
				getAddress(OnLat, OnLng);
			}else{
				for (var p = 1; p <= path.length; ++p) {
					leg = Math.round(geo.computeDistanceBetween(point, path[p]));
					d1 = distance + 0
					distance += leg;
					overflow = dist - (d1 % dist);

					if (distance >= dist && leg >= overflow) {
						if (overflow && leg >= overflow) {
							pos = geo.computeOffset(point, overflow, geo.computeHeading(point, path[p]));
							opts.position = pos;
							var OnDesLat = opts.position.lat();
							var OnDesLng = opts.position.lng();
							
							var diffOnLat = floorFigure(OnDesLat) - floorFigure(OnLat);
							var OnLatDiff = (diffOnLat < 0) ? diffOnLat * -1 : diffOnLat;
							var diffOnLng = floorFigure(OnDesLng) - floorFigure(OnLng);
							var OnLngDiff = (diffOnLng < 0) ? diffOnLng * -1 : diffOnLng;
							
							if(((OnLatDiff <= 0.008) && (OnLngDiff <= 0.007))){
								getAddress(OnLat, OnLng);
								asd = asd + 1;
								markers.push(new google.maps.Marker(opts));
							}         
							distance -= dist;
						}
						
						/*
						while (distance >= dist) {
							pos = geo.computeOffset(point, dist + overflow, geo.computeHeading(point, path[p]));
							opts.position = pos;
							markers.push(new google.maps.Marker(opts));
							distance -= dist;
						}*/
					}
					point = path[p];
				}
			}
			return markers;
        }

        ///////////// getAddress functions///////////////
        var getAddress = function (StepLatitude, StepLongitude) {
                var url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=" + StepLatitude + "," + StepLongitude + "&sensor=false";
                jQuery.getJSON(url, function(json) {
                    Address = Create_Address(json);
                    if (Address != 0) {
                        NewAddress = "Co-ordinates - " + StepLatitude + ", " + StepLongitude +"<br>Address - " + Address + "<br><br>";
                    }
                    $("#location-address").append(NewAddress);
                });
            };
            /*
             * Create an address out of the json
             */
        function Create_Address(json, callback) {
            if (!check_status(json)) // If the json file's status is not ok, then return
                return 0;
            /*address['country'] = google_getCountry(json);
            address['province'] = google_getProvince(json);
            address['city'] = google_getCity(json);
            address['street'] = google_getStreet(json);
            address['postal_code'] = google_getPostalCode(json);
            address['country_code'] = google_getCountryCode(json);
            address['formatted_address'] = google_getAddress(json);*/
            var Address = google_getAddress(json);
            return Address;
            callback();
        }
        /* 
         * Check if the json data from Google Geo is valid
         */
        function check_status(json) {
            if (json["status"] == "OK") return true;
            return false;
        }
        /*
         * Given Google Geocode json, return the value in the specified element of the array
         */

        function google_getCountry(json) {
            return Find_Long_Name_Given_Type("country", json["results"][0]["address_components"], false);
        }

        function google_getProvince(json) {
            return Find_Long_Name_Given_Type("administrative_area_level_1", json["results"][0]["address_components"], true);
        }

        function google_getCity(json) {
            return Find_Long_Name_Given_Type("locality", json["results"][0]["address_components"], false);
        }

        function google_getStreet(json) {
            return Find_Long_Name_Given_Type("street_number", json["results"][0]["address_components"], false) + ' ' + Find_Long_Name_Given_Type("route", json["results"][0]["address_components"], false);
        }

        function google_getPostalCode(json) {
            return Find_Long_Name_Given_Type("postal_code", json["results"][0]["address_components"], false);
        }

        function google_getCountryCode(json) {
            return Find_Long_Name_Given_Type("country", json["results"][0]["address_components"], true);
        }

        function google_getAddress(json) {
            return json["results"][0]["formatted_address"];
        }
		//=====Getting upto 3 digit coordinates=======
		function floorFigure(figure){
		var digits = $("#digits").find(':selected').val();
			var d = Math.pow(10,digits);
			return (parseInt(figure*d)/d).toFixed(digits);
		};
			