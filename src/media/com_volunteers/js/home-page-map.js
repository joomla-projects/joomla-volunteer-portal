/**
 * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
if (!Joomla) {
    throw new Error('Joomla API is not properly initialized');
}

if (Joomla.getOptions('com_volunteers_maps')) {
    let options = Joomla.getOptions('com_volunteers_maps');
    let joomlers = JSON.parse(options.markers);

    let map = L.map('map-canvas').setView([51.505, -0.09], 1);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);


    let myIcon = L.icon({
        iconUrl: 'media/com_volunteers/images/joomla.png',
        iconSize: [50, 50],
        iconAnchor: [0, 0],
        popupAnchor: [0, 0]
    });

    let markers = L.markerClusterGroup();

    for (let i = 0; i < joomlers.length; i++) {
        let a = JSON.parse(joomlers[i]);
        let marker = L.marker(new L.LatLng(a.lat, a.lng), { icon: myIcon});
        marker.bindPopup('<div style="width:200px;"><img width="40" className="pull-left" style="padding-right: 10px"   src="' + a.image + '"/><a href="' + a.url + '">' + a.title + '</a></div>');
        markers.addLayer(marker);
    }

    map.addLayer(markers);
}

