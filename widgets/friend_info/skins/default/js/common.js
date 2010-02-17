var latlngbounds, lat, lng, marker = {};
var friends_map;

// from http://just3ws.wordpress.com/2007/04/28/javascript-stringformat-method/#comment-142
String.prototype.format = function()
{
    var pattern = /\{\w+\}/g;
    var args = arguments;
    return this.replace(pattern, function(capture){ return args[0][capture.match(/\w+/)]; });
}

function DateTimeFormat(text) {
    if(text)
        return new String("{yy}/{mm}/{dd} {hh}:{nn}:{ss}").format({
            "yy":text.substr(0,4),
            "mm":text.substr(4,2),
            "dd":text.substr(6,2),
            "hh":text.substr(8,2),
            "nn":text.substr(10,2),
            "ss":text.substr(12,2)
           });
    else return;
}

function toggle(id)
{
    id = xGetElementById(id);
    if(id.style)
        id.style.display = (id.style.display == 'none') ? '' : 'none';
}


function getLocation() {
    navigator.geolocation.getCurrentPosition(getLocationSuccess, errorHandler, {timeout:10000});
}

function getLocationSuccess(position) {
    lat = position.coords.latitude;
    lng = position.coords.longitude;
}

function updateit(form) {
    if(form.geolocation)
        form.geolocation.value = lat + ',' + lng;
}

function errorHandler(e) {
    alert('Error code: ' + e);
}

function createMarker(map, member_srl, nickname, latlng) {
    if (GBrowserIsCompatible())
    {
        var geocoder = new GClientGeocoder();
        var marker = new GMarker(latlng, {draggable: false});

        if (latlng != null) {
            latlngbounds.extend( latlng );
            geocoder.getLocations(latlng, function(response){
                if (!response || response.Status.code != 200) {
                    alert("Status Code: " + response.Status.code);
                } else {
                    var place = response.Placemark[0];
                    var message = xe.lang.nick_name + ': <a href="#" onclick="return false;" class="strong member_' +
                        member_srl +'" >'+ nickname +'</a>' +
                        '<br />Geocode: ' + marker.getLatLng() +
                        '<br />'+ xe.lang.geolocation +': ' + place.address
                    GEvent.addListener(marker, "click", function(){
                        map.panTo(latlng);
                        marker.openInfoWindowHtml(message);
                    });
                }
            });
        }
        return marker;
    }
}

function init(map, id, members) {
    if (GBrowserIsCompatible())
    {
        latlngbounds = new GLatLngBounds();
        map = new GMap2(id);
        map.setUIToDefault();
        var mgr = new GMarkerManager(map);
        for (var i=0; i < members.length; i++)
        {
            if(i == 0) {
                if(lat && lng)
                    members[i].latlng = new GLatLng(lat, lng);
                lat = lng = null;
            }

            marker[i] = createMarker(map, members[i].member_srl,
                members[i].nickname,
                members[i].latlng);
            map.addOverlay(marker[i]);
            mgr.addMarker(marker[i]);
        }
        mgr.refresh();
        map.setCenter( latlngbounds.getCenter( ), map.getBoundsZoomLevel( latlngbounds ) );
    }
    return map;
}

function showMemberInfo(container, member) {
    if (member)
    {
        var join_date = member.join_date;
        var last_login = member.last_login;

        var basic_info = '<ul>' +
            '<li class="member_'+ member.member_srl +'">User Name: ' + member.username + '</li>' +
            '<li>Nick Name: ' + member.nickname + '</li>' +
            '<li>E-mail: ' + member.email_address + '</li>' +
            '</ul>';

        var detail_info = '<ul>' +
            '<li>Join Date: '+ join_date + '</li>' +
            '<li>Last Login: ' + last_login + '</li>';
        if (member.homepage)
            detail_info += '<li>Homepage: ' + member.homepage + '</li>';
        if (member.blog)
            detail_info += '<li>Blog: ' + member.blog + '</li>';
        if (member.description)
            detail_info += '<li>Description: ' + member.description + '</li>';
        detail_info += '</ul>';

        xGetElementById(container.basic).innerHTML = basic_info;
        xGetElementById(container.detail).innerHTML = detail_info;
    }
}

function dispFriendsInfo(id, info, value) {
    id.panTo(marker[value].getLatLng());
    showMemberInfo({
        'basic':'friendsBasicInfoContainer',
        'detail':'friendsDetailInfoContainer'},
        info.members[value]);
}

function completeUpdateGeolocation(fo_obj) {

}

function completeGetMeetingInfoList(fo_obj) {
    var output = fo_obj['output'];
    var url = current_url.setQuery('module', 'meetinginfo');
    url = url.setQuery('act', 'dispMeetinginfoAdd');
    url = url.setQuery('mid', '');
    if (output != null)
        dispFriendsMeetingInfo(output.item, url);
    else {
        url = url.setQuery('guest_srl', document.forms.update_geolocation.guest_srl.value);
        var content = '<ul><li>' + xe.lang.no_meeting_info + '</li>' +
        '<a class="button black strong" onclick="popopen(this.href, \'popup\'); return false;" href="' +
         url + '"><span>' + xe.lang.add_meeting_info + '</span></a></ul>';
       xGetElementById('friendsMeetingInfoContainer').innerHTML = content;
    }
}

function dispFriendsMeetingInfo(item, url) {
    var meeting_list = '<li>' + xe.lang.no_meeting_info + '</li>';

    if (item.length) {
        meeting_list = '';
        for (var i = 0; i < item.length; i++)
        {
            meeting_list += '<li class="list" title="'+ DateTimeFormat(item[i].regdate) + '"><span>' + item[i].content + '</span></li>';
        }
        url = url.setQuery('guest_srl', item[0].guest_srl);
    } else if (item.guest_srl) {
        meeting_list = '<li class="list" title="' + DateTimeFormat(item.regdate) + '"><span>' + item.content + '</span></li>';
        url = url.setQuery('guest_srl', item.guest_srl);
    }

    var content = '<ul class="item">' + meeting_list +
        '<a class="button black strong" onclick="popopen(this.href, \'popup\'); return false;" href="' +
        url + '"><span>' + xe.lang.add_meeting_info + '</span></a>'
        '</ul>';
    xGetElementById('friendsMeetingInfoContainer').innerHTML = content;
}
