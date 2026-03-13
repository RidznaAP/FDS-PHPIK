<table>
    <thead>
        <tr>
            <th rowspan="3" align="center" valign="center" style="font-weight: bold; background-color: #003366; color: #FFFFFF;">PROVINSI/<br>BBKHIT/BKHIT</th>
            <th rowspan="3" align="center" valign="center" style="font-weight: bold; background-color: #003366; color: #FFFFFF;">NO.</th>
            <th rowspan="3" align="center" valign="center" style="font-weight: bold; background-color: #003366; color: #FFFFFF;">KABUPATEN/<br>KOTA</th>
            <th rowspan="3" align="center" valign="center" style="font-weight: bold; background-color: #003366; color: #FFFFFF;">JENIS MP</th>
            <th rowspan="3" align="center" valign="center" style="font-weight: bold; background-color: #003366; color: #FFFFFF;">JENIS HPIK</th>
            <th rowspan="3" align="center" valign="center" style="font-weight: bold; background-color: #003366; color: #FFFFFF;">KEMAMPUAN<br>UJI UPT</th>
            <th rowspan="3" align="center" valign="center" style="font-weight: bold; background-color: #003366; color: #FFFFFF;">METODE<br>PENGUJIAN</th>
            <th rowspan="3" align="center" valign="center" style="font-weight: bold; background-color: #003366; color: #FFFFFF;">LAB UJI</th>
            <th rowspan="3" align="center" valign="center" style="font-weight: bold; background-color: #003366; color: #FFFFFF;">TARGET<br>UJI</th>
            <th rowspan="3" align="center" valign="center" style="font-weight: bold; background-color: #003366; color: #FFFFFF;">JUMLAH<br>PENGUJIAN<br>YANG<br>DILAKUKAN</th>
            <th colspan="4" align="center" valign="center" style="font-weight: bold; background-color: #003366; color: #FFFFFF;">WAKTU PELAKSANAAN</th>
            <th rowspan="3" align="center" valign="center" style="font-weight: bold; background-color: #003366; color: #FFFFFF;">TOTAL<br>PENGUJIAN</th>
            <th rowspan="3" align="center" valign="center" style="font-weight: bold; background-color: #003366; color: #FFFFFF;">LOKASI PENGAMBILAN<br>SAMPEL</th>
            <th rowspan="3" align="center" valign="center" style="font-weight: bold; background-color: #003366; color: #FFFFFF;">JUMLAH<br>SAMPEL</th>
            <th rowspan="3" align="center" valign="center" style="font-weight: bold; background-color: #003366; color: #FFFFFF;">METODE<br>PENGAMBILAN<br>SAMPEL</th>
        </tr>
        <tr>
            <th colspan="2" align="center" valign="center" style="font-weight: bold; background-color: #003366; color: #FFFFFF;">Periode I</th>
            <th colspan="2" align="center" valign="center" style="font-weight: bold; background-color: #003366; color: #FFFFFF;">Periode II</th>
        </tr>
        <tr>
            <th align="center" valign="center" style="font-weight: bold; background-color: #003366; color: #FFFFFF;">TW 1</th>
            <th align="center" valign="center" style="font-weight: bold; background-color: #003366; color: #FFFFFF;">TW 2</th>
            <th align="center" valign="center" style="font-weight: bold; background-color: #003366; color: #FFFFFF;">TW 3</th>
            <th align="center" valign="center" style="font-weight: bold; background-color: #003366; color: #FFFFFF;">TW 4</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $row)
        <tr>
            <td valign="center">{{ $row->provinsi }}</td>
            <td align="center" valign="center">{{ $index + 1 }}</td>
            <td valign="center">{{ $row->kab_kota }}</td>
            <td valign="center">{{ $row->jenis_mp }}</td>
            <td valign="center">{{ $row->jenis_hpik }}</td>
            <td valign="center">{{ $row->kemampuan_uji_upt }}</td>
            <td valign="center">{{ $row->metode_pengujian }}</td>
            <td valign="center">{{ $row->lab_uji }}</td>
            <td align="center" valign="center">{{ $row->target_uji }}</td>
            <td align="center" valign="center"></td>
            <td align="center" valign="center">{{ $row->tw1 }}</td>
            <td align="center" valign="center">{{ $row->tw2 }}</td>
            <td align="center" valign="center">{{ $row->tw3 }}</td>
            <td align="center" valign="center">{{ $row->tw4 }}</td>
            <td align="center" valign="center">{{ $row->total_pengujian }}</td>
            <td valign="center">{{ $row->rencana_lokasi }}</td>
            <td align="center" valign="center">{{ $row->rencana_jumlah_sampel }}</td>
            <td valign="center">{{ $row->rencana_metode_sampling }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
