<h1>Daftar Pegawai</h1>
<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>NIP</th>
            <th>Nama</th>
            <th>Jabatan</th>
            <th>Masa Kerja</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($pegawai as $p): ?>
        <tr>
            <td><?= $p['nip'] ?></td>
            <td><?= $p['nama'] ?></td>
            <td><?= $p['jabatan'] ?></td>
            <td><?= $p['masa_kerja'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
