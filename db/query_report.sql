SELECT
COALESCE(a.tpkp, a.tppi) as tanggal,
COALESCE(a.kckp, a.kcpi) as kecamatan,
a.pmkp as pakai_masker,
a.tpmkp as tidak_pakai_masker,
a.jjkp as jaga_jarak,
a.tjjkp as tidak_jaga_jarak,
a.fctpi as fasilitas_cuci_tangan,
a.sppi as sosialisasi_prokes,
a.cstpi as cek_suhu_tubuh,
a.ppppi as petugas_pengawas_prokes,
a.dbpi as desinfeksi_berkala,
round(((a.pmkp+a.tpmkp)/(a.pmkp+a.tpmkp+a.jjkp+a.tjjkp)*100),1) as kepatuhan_individu
FROM
(
SELECT
    kepatuhan_prokes.tanggal_pantau tpkp,
    kepatuhan_prokes.kode_kecamatan kckp,
    kepatuhan_prokes.pakai_masker pmkp,
    kepatuhan_prokes.tidak_pakai_masker tpmkp,
    kepatuhan_prokes.jaga_jarak jjkp,
    kepatuhan_prokes.tidak_jaga_jarak tjjkp,
    prokes_institusi.kecamatan_id kcpi,
    prokes_institusi.tanggal_pantau tppi,
    prokes_institusi.fasilitas_cuci_tangan as fctpi,
    prokes_institusi.sosialisasi_prokes as sppi,
    prokes_institusi.cek_suhu_tubuh as cstpi,
    prokes_institusi.petugas_pengawas_prokes as ppppi,
    prokes_institusi.desinfeksi_berkala as dbpi
FROM kepatuhan_prokes
LEFT JOIN prokes_institusi
ON kepatuhan_prokes.tanggal_pantau=prokes_institusi.tanggal_pantau
UNION
SELECT
    kepatuhan_prokes.tanggal_pantau tpkp,
    kepatuhan_prokes.kode_kecamatan kckp,
    kepatuhan_prokes.pakai_masker pmkp,
    kepatuhan_prokes.tidak_pakai_masker tpmkp,
    kepatuhan_prokes.jaga_jarak jjkp,
    kepatuhan_prokes.tidak_jaga_jarak tjjkp,
    prokes_institusi.kecamatan_id kcpi,
    prokes_institusi.tanggal_pantau tppi,
    prokes_institusi.fasilitas_cuci_tangan as fctpi,
    prokes_institusi.sosialisasi_prokes as sppi,
    prokes_institusi.cek_suhu_tubuh as cstpi,
    prokes_institusi.petugas_pengawas_prokes as ppppi,
    prokes_institusi.desinfeksi_berkala as dbpi
FROM kepatuhan_prokes
RIGHT JOIN prokes_institusi
ON kepatuhan_prokes.tanggal_pantau=prokes_institusi.tanggal_pantau) a