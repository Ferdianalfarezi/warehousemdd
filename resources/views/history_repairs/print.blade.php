<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Print Request Repair — {{ $history->no }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #fff;
            font-family: Arial, sans-serif;
        }

        @media print {
            @page { margin: 0; size: A4 portrait; }
            body { margin: 0; }
            .repair-card { page-break-after: always; page-break-inside: avoid; }
            .no-print { display: none !important; }
        }

        .no-print {
            text-align: center;
            padding: 12px 0 8px;
        }
        .print-btn {
            background: #18181b; color: #fff; border: none;
            padding: 9px 28px; font-size: 14px; border-radius: 6px;
            cursor: pointer;
        }
        .print-btn:hover { background: #000; }

        /*
         * A4 @ 96dpi = 794px × 1123px
         * Semua posisi pakai px, origin: top-left card
         */
        .repair-card {
            width: 794px;
            height: 1123px;
            position: relative;
            overflow: hidden;
            margin-bottom: 8px;
            background-image: url('/images/requestrepairbg.png');
            background-repeat: no-repeat;
            background-position: top left;
            background-size: 794px 1123px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        /* ── Base classes ── */
        .val, .lbl {
            position: absolute;
            font-family: Arial, sans-serif;
            color: #18181b;
        }
        .lbl {
            font-size: 7px;
            font-weight: 700;
           
            letter-spacing: 0.4px;
            color: #71717a;
            white-space: nowrap;
            text-align: center;
        }
        .val {
            font-size: 10px;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .val-wrap {
            white-space: normal;
            word-break: break-word;
        }
        .val-bold  { font-weight: 700; }
        .val-mono  { font-family: 'Courier New', monospace; }
        .val-large { font-size: 16px; font-weight: 700; }
        .val-xl    { font-size: 20px; font-weight: 700; }

        /* ── BADGE: OK / NG ── */
        .badge-ok {
            display: inline-block;
            background: #dcfce7; color: #166534;
            border: 1px solid #bbf7d0;
            font-size: 8px; font-weight: 700;
            padding: 1px 6px; border-radius: 3px;
        }
        .badge-ng {
            display: inline-block;
            background: #fee2e2; color: #991b1b;
            border: 1px solid #fecaca;
            font-size: 8px; font-weight: 700;
            padding: 1px 6px; border-radius: 3px;
        }
        .badge-none {
            color: #a1a1aa; font-size: 9px;
        }

        /* ══════════════════════════════════════════
           LABEL positions
           ══════════════════════════════════════════ */

        /* ── Header area ── */
        .lbl-company            { left: 38px;  top: 70px; font-size: 10px; font-weight: 700; color: #18181b; text-transform: none; letter-spacing: 0; }
        .lbl-logo               { left: 78px; top: 27px; width: 80px; }
        .lbl-logo img           { width: 70px; height: auto; display: block; }
        .lbl-tittle             { left: 208px;  top: 42px; font-size: 20px; font-weight: 900; color: #18181b;  }
        .lbl-docno              { left: 575px;  top: 30px; font-size: 10px; font-weight: 500; color: #18181b;  }
        .lbl-tanggal            { left: 574px;  top: 45px; font-size: 10px; font-weight: 500; color: #18181b;  }
        .lbl-norev              { left: 575px;  top: 60px; font-size: 10px; font-weight: 500; color: #18181b;  }
        .lbl-hal                { left: 575px;  top: 75px; font-size: 10px; font-weight: 500; color: #18181b;  }

        .lbl-datadies           { left: 305px;  top: 99px; font-size: 10px; font-weight: 900; color: #18181b;  }
        .lbl-jenisdies          { left: 60px;  top: 125px; font-size: 10px; font-weight: 700; color: #18181b;  }
        .lbl-internal           { left: 230px;  top: 125px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-eksternal          { left: 448px;  top: 125px; font-size: 10px; font-weight: 700; color: #18181b; }

        .lbl-tanggalpengajuan   { left: 32px;  top: 148px; font-size: 10px; font-weight: 700; color: #18181b;  }
        .lbl-group              { left: 32px;  top: 165px; font-size: 10px; font-weight: 700; color: #18181b;  }
        .lbl-shift              { left: 32px;  top: 183px; font-size: 10px; font-weight: 700; color: #18181b;  }
        .lbl-stroke             { left: 32px;  top: 201px; font-size: 10px; font-weight: 700; color: #18181b;  }
        .lbl-line               { left: 423px;  top: 148px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-partno             { left: 423px;  top: 165px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-nama               { left: 423px;  top: 183px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-customer           { left: 423px;  top: 201px; font-size: 10px; font-weight: 700; color: #18181b; }

        .lbl-identifikasi       { left: 265px;  top: 215px; font-size: 10px; font-weight: 900; color: #18181b; }
        .lbl-gambar             { left: 80px;  top: 232px; font-size: 10px; font-weight: 900; color: #18181b;  }
        .lbl-lingkari           { left: 570px;  top: 234px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-kategoriproblem    { left: 513px;  top: 250px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-std                { left: 655px;  top: 250px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-act                { left: 720px;  top: 250px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-burry              { left: 510px;  top: 265px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-dimensi            { left: 510px;  top: 278px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-scrath             { left: 510px;  top: 292px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-crack              { left: 510px;  top: 306px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-dll                { left: 510px;  top: 321px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-gl                 { left: 535px;  top: 334px; font-size: 10px; font-weight: 500; color: #18181b; }
        .lbl-op                 { left: 680px;  top: 334px; font-size: 10px; font-weight: 500; color: #18181b; }

        .lbl-tindakan           { left: 265px;  top: 407px; font-size: 10px; font-weight: 900; color: #18181b; }
        .lbl-penyebab           { left: 70px;  top: 426px; font-size: 10px; font-weight: 700; color: #18181b;  }
        .lbl-perbaikan          { left: 320px;  top: 426px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-catatan            { left: 550px;  top: 426px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-problemburry       { left: 100px;  top: 551px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-standart           { left: 450px;  top: 551px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-statusrepair       { left: 602px;  top: 549px; font-size: 10px; font-weight: 900; color: #18181b; }
        .lbl-item               { left: 70px;   top: 580px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-prosesgrinding     { left: 161px;  top: 573px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-shim               { left: 280px;  top: 580px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-status1            { left: 375px;  top: 569px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-status2            { left: 453px;  top: 569px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-temporary          { left: 558px;  top: 568px; font-size: 10px; font-weight: 900; color: #18181b; }
        .lbl-permanent          { left: 670px;  top: 568px; font-size: 10px; font-weight: 900; color: #18181b; }
        .lbl-ok1                { left: 374px;  top: 588px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-ng1                { left: 400px;  top: 588px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-ok2                { left: 438px;  top: 588px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-ng2                { left: 498px;  top: 588px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-gl2                { left: 560px;  top: 588px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-op2                { left: 680px;  top: 588px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-pastikan           { left: 50px;  top: 657px; font-size: 9px; font-weight: 700; color: #18181b;   }

        .lbl-target             { left: 290px;  top: 673px; font-size: 10px; font-weight: 900; color: #18181b; }
        .lbl-plan               { left: 70px;  top: 699px; font-size: 10px; font-weight: 900; color: #18181b;  }
        .lbl-actual             { left: 180px;  top: 699px; font-size: 10px; font-weight: 900; color: #18181b; }
        .lbl-remark1            { left: 400px;  top: 699px; font-size: 10px; font-weight: 900; color: #18181b; }
        .lbl-checked1           { left: 606px;  top: 695px; font-size: 7px; font-weight: 700; color: #18181b;  }
        .lbl-judge1             { left: 705px;  top: 698px; font-size: 12px; font-weight: 900; color: #18181b; }
        .lbl-mdd1               { left: 606px;  top: 710px; font-size: 9px; font-weight: 700; color: #18181b;  }
        .lbl-psd1               { left: 638px;  top: 710px; font-size: 9px; font-weight: 700; color: #18181b;  }
        .lbl-qcd1               { left: 670px;  top: 710px; font-size: 9px; font-weight: 700; color: #18181b;  }

        .lbl-konfirmasi         { left: 150px;  top: 748px; font-size: 10px; font-weight: 900; color: #18181b; }
        .lbl-monitoringdies     { left: 148px;  top: 770px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-tanggalcek         { left: 62px;  top: 790px; font-size: 10px; font-weight: 700; color: #18181b;  }
        .lbl-lotprod            { left: 145px;  top: 795px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-awal               { left: 242px;  top: 790px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-tengah             { left: 279px;  top: 790px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-akhir              { left: 330px;  top: 790px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-qty                { left: 408px;  top: 790px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-remark2            { left: 515px;  top: 795px; font-size: 10px; font-weight: 700; color: #18181b; }
        .lbl-checked2           { left: 605px;  top: 790px; font-size: 7px; font-weight: 700; color: #18181b;  }
        .lbl-judge2             { left: 705px;  top: 795px; font-size: 12px; font-weight: 900; color: #18181b; }
        .lbl-mdd2               { left: 606px;  top: 806px; font-size: 9px; font-weight: 700; color: #18181b;  }
        .lbl-psd2               { left: 638px;  top: 806px; font-size: 9px; font-weight: 700; color: #18181b;  }
        .lbl-qcd2               { left: 670px;  top: 806px; font-size: 9px; font-weight: 700; color: #18181b;  }

        .lbl-targetpermanent    { left: 290px;  top: 890px; font-size: 10px; font-weight: 900; color: #18181b; }
        .lbl-plan2              { left: 55px;  top: 915px; font-size: 10px; font-weight: 900; color: #18181b;  }
        .lbl-actual2            { left: 125px;  top: 915px; font-size: 10px; font-weight: 900; color: #18181b; }
        .lbl-rcd                { left: 245px;  top: 915px; font-size: 10px; font-weight: 900; color: #18181b; }
        .lbl-recovery           { left: 435px;  top: 915px; font-size: 10px; font-weight: 900; color: #18181b; }
        .lbl-atc                { left: 510px;  top: 915px; font-size: 8px; font-weight: 900; color: #18181b;  }
        .lbl-checked3           { left: 605px;  top: 910px; font-size: 7px; font-weight: 700; color: #18181b;  }
        .lbl-judge3             { left: 705px;  top: 915px; font-size: 12px; font-weight: 900; color: #18181b; }
        .lbl-mdd3               { left: 606px;  top: 926px; font-size: 9px; font-weight: 700; color: #18181b;  }
        .lbl-psd3               { left: 638px;  top: 926px; font-size: 9px; font-weight: 700; color: #18181b;  }
        .lbl-qcd3               { left: 670px;  top: 926px; font-size: 9px; font-weight: 700; color: #18181b;  }
    </style>
    </style>
    </style>
</head>
<body>

<div class="repair-card">

    {{-- ══ HEADER ══ --}}
    <div class="lbl lbl-company">PT SARI TAKAGI ELOK PRODUK</div>
    <div class="lbl lbl-logo">
        <img src="{{ asset('images/logostep.png') }}" alt="Logo STEP">
    </div>
    <div class="lbl lbl-tittle">PERMINTAAN PERBAIKAN DIES</div>
    <div class="lbl lbl-docno">No. Dokumen</div>
    <div class="lbl lbl-tanggal">Tanggal diterbitkan</div>
    <div class="lbl lbl-norev">No. Revisi</div>
    <div class="lbl lbl-hal">Halaman</div>

    <div class="lbl lbl-datadies">DATA DIES <i>(Diisi Oleh PSD)</i></div>
    <div class="lbl lbl-jenisdies">JENIS DIES&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</div>
    <div class="lbl lbl-internal">Milik Internal PT.STEP</div>
    <div class="lbl lbl-eksternal">Milik Eksternal (Konsumen)</div>

    <div class="lbl lbl-tanggalpengajuan">TANGGAL PENGAJUAN</div>
    <div class="lbl lbl-group">GROUP</div>
    <div class="lbl lbl-shift">SHIFT</div>
    <div class="lbl lbl-stroke">STROKE</div>
    <div class="lbl lbl-line">LINE / MESIN</div>
    <div class="lbl lbl-partno">PART NO</div>
    <div class="lbl lbl-nama">NAMA DAN NO.PROSES</div>
    <div class="lbl lbl-customer">CUSTOMER</div>

    <div class="lbl lbl-identifikasi">INDENTIFIKASI MASALAH <i>(Diisi Oleh PSD)</i></div>
    <div class="lbl lbl-gambar">Gambar / Sketch Part / Dies Problem (Untuk Visual Control)</i></div>
    <div class="lbl lbl-lingkari"><i>(Lingkari Nomor Problem)</i></div>
    <div class="lbl lbl-kategoriproblem">KATEGORI PROBLEM</div>
    <div class="lbl lbl-std">STD</div>
    <div class="lbl lbl-act">ACT</div>
    <div class="lbl lbl-burry">1.&nbsp;&nbsp;BURRY</div>
    <div class="lbl lbl-dimensi">2.&nbsp;&nbsp;NG DIMENSI</div>
    <div class="lbl lbl-scrath">3.&nbsp;&nbsp;SCRATH / DENTED</div>
    <div class="lbl lbl-crack">4.&nbsp;&nbsp;CRACK</div>
    <div class="lbl lbl-dll">5.&nbsp;&nbsp;DLL</div>
    <div class="lbl lbl-gl">Group Leader</div>
    <div class="lbl lbl-op">Operator</div>

    <div class="lbl lbl-tindakan">TINDAKAN PERBAIKAN <i>(Diisi Oleh MDD)</i></div>
    <div class="lbl lbl-penyebab">ANALISA PENYEBAB</div>
    <div class="lbl lbl-perbaikan">TINDAKAN PERBAIKAN</div>
    <div class="lbl lbl-catatan">CATATAN PENGGANTIAN SPAREPART</div>
    <div class="lbl lbl-problemburry"><i>Penanganan Problem Burry (Proses Grinding)</i></div>
    <div class="lbl lbl-standart"><i>Standard</i></div>
    <div class="lbl lbl-statusrepair"><i>STATUS REPAIR</i></div>
    <div class="lbl lbl-item">ITEM</div>
    <div class="lbl lbl-prosesgrinding">PROSES<br>GRINDING</br></div>
    <div class="lbl lbl-shim">SHIM UP</div>
    <div class="lbl lbl-status1">STATUS</div>
    <div class="lbl lbl-status2">STATUS</div>
    <div class="lbl lbl-temporary">TEMPORARY</div>
    <div class="lbl lbl-permanent">PERMANENT</div>
    <div class="lbl lbl-ok1">OK</div>
    <div class="lbl lbl-ng1">NG</div>
    <div class="lbl lbl-ok2">OK</div>
    <div class="lbl lbl-ng2">NG</div>
    <div class="lbl lbl-gl2">Group Leader</div>
    <div class="lbl lbl-op2">Operator</div>
    <div class="lbl lbl-pastikan">PASTIKAN SPAREPART YANG DI GRINDING DI SHIM UP SESUAI DENGAN SIZE PROSES GRINDING</div>

    <div class="lbl lbl-target">TARGET TRIAL AFTER REPAIR</div>
    <div class="lbl lbl-plan">Plan</div>
    <div class="lbl lbl-actual">Actual</div>
    <div class="lbl lbl-remark1">REMARK</div>
    <div class="lbl lbl-checked1">Checked (GroupLeader)</div>
    <div class="lbl lbl-judge1">JUDGE</div>
    <div class="lbl lbl-mdd1">MDD</div>
    <div class="lbl lbl-psd1">PSD</div>
    <div class="lbl lbl-qcd1">QCD</div>

    <div class="lbl lbl-konfirmasi">KONFIRMASI MONITORING HASIL PERAWATAN & PERBAIKAN (DIISI MDD, PSD, QCD)</div>
    <div class="lbl lbl-monitoringdies">MONITORING DIES REPAIR TEMPORARY [WELDING, POLES SCRATCH,, GRINDING, + SHIME UP]</div>
    <div class="lbl lbl-tanggalcek">Tanggal<br>Cek</br></div>
    <div class="lbl lbl-lotprod">LOT PROD</div>
    <div class="lbl lbl-awal">AWAL</div>
    <div class="lbl lbl-tengah">TENGAH</div>
    <div class="lbl lbl-akhir">AKHIR</div>
    <div class="lbl lbl-qty">QTY</div>
    <div class="lbl lbl-remark2">REMARK</div>
    <div class="lbl lbl-checked2">Checked (GroupLeader)</div>
    <div class="lbl lbl-judge2">JUDGE</div>
    <div class="lbl lbl-mdd2">MDD</div>
    <div class="lbl lbl-psd2">PSD</div>
    <div class="lbl lbl-qcd2">QCD</div>

    <div class="lbl lbl-targetpermanent">TARGET PERMANENT ACTION</div>
    <div class="lbl lbl-plan2">Plan</div>
    <div class="lbl lbl-actual2">Actual</div>
    <div class="lbl lbl-rcd">Root Cause Delay</div>
    <div class="lbl lbl-recovery">Recovery</div>
    <div class="lbl lbl-atc">Assy+Trial+Check</div>
    <div class="lbl lbl-checked3">Checked (GroupLeader)</div>
    <div class="lbl lbl-judge3">JUDGE</div>
    <div class="lbl lbl-mdd3">MDD</div>
    <div class="lbl lbl-psd3">PSD</div>
    <div class="lbl lbl-qcd3">QCD</div>

</body>
</html>