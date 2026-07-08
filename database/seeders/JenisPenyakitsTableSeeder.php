<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class JenisPenyakitsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('jenis_penyakits')->delete();
        
        \DB::table('jenis_penyakits')->insert(array (
            0 => 
            array (
                'id' => 51,
                'nama' => 'Infection with ictalurid herpesvirus-1',
                'organisme_penyebab' => 'Ictalurid herpesvirus-1',
                'singkatan' => NULL,
                'golongan' => 'Virus',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-05-11 04:01:09',
                'updated_at' => '2026-05-11 04:01:09',
            ),
            1 => 
            array (
                'id' => 52,
                'nama' => 'Infection with ictalurid herpesvirus-2',
                'organisme_penyebab' => 'Ictalurid herpesvirus-2',
                'singkatan' => NULL,
                'golongan' => 'Virus',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-05-11 04:01:10',
                'updated_at' => '2026-05-11 04:01:10',
            ),
            2 => 
            array (
                'id' => 53,
                'nama' => 'Infection with spring viraemia of carp virus',
            'organisme_penyebab' => 'Spring viraemia of carp virus (SVCV)',
                'singkatan' => NULL,
                'golongan' => 'Virus',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-05-11 04:01:10',
                'updated_at' => '2026-05-11 04:01:10',
            ),
            3 => 
            array (
                'id' => 54,
                'nama' => 'Infection with infectious haematopoietic necrosis virus',
            'organisme_penyebab' => 'Infectious   haematopoietic necrosis virus   (IHNV)',
                'singkatan' => NULL,
                'golongan' => 'Virus',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-05-11 04:01:10',
                'updated_at' => '2026-05-11 04:01:10',
            ),
            4 => 
            array (
                'id' => 55,
                'nama' => 'Infection with Megalocytivirus',
            'organisme_penyebab' => 'Red sea bream iridovirus (RSIV)',
                'singkatan' => NULL,
                'golongan' => 'Virus',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-05-11 04:01:10',
                'updated_at' => '2026-05-11 04:01:10',
            ),
            5 => 
            array (
                'id' => 56,
                'nama' => 'Infection with Megalocytivirus',
            'organisme_penyebab' => 'Infectious spleen and kidney necrosis virus (ISKNV) / Megalocytivirus pagrus 1',
                'singkatan' => NULL,
                'golongan' => 'Virus',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-05-11 04:01:10',
                'updated_at' => '2026-05-11 04:01:10',
            ),
            6 => 
            array (
                'id' => 57,
                'nama' => 'Infection with Megalocytivirus',
            'organisme_penyebab' => 'Turbot reddish body iridovirus (TRBIV)',
                'singkatan' => NULL,
                'golongan' => 'Virus',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-05-11 04:01:10',
                'updated_at' => '2026-05-11 04:01:10',
            ),
            7 => 
            array (
                'id' => 58,
                'nama' => 'Grouper iridoviral disease',
            'organisme_penyebab' => 'Grouper iridovirus (GIV)/ Grouper iridovirus of Taiwan (TGIV)/Singapore grouper iridovirus (SGIV)',
                'singkatan' => NULL,
                'golongan' => 'Virus',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-05-11 04:01:10',
                'updated_at' => '2026-05-11 04:01:10',
            ),
            8 => 
            array (
                'id' => 59,
                'nama' => 'Infection with epizootic haematopoietic necrosis virus',
            'organisme_penyebab' => 'Epizootic hematopoietic necrosis virus (EHNV)',
                'singkatan' => NULL,
                'golongan' => 'Virus',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-05-11 04:01:10',
                'updated_at' => '2026-05-11 04:01:10',
            ),
            9 => 
            array (
                'id' => 60,
            'nama' => 'Viral   encephalopathy and retinopthy (VER)/Viral nervous necrosis (VNN)',
                'organisme_penyebab' => 'Nervous necrosis virus',
                'singkatan' => NULL,
                'golongan' => 'Virus',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-05-11 04:01:10',
                'updated_at' => '2026-05-11 04:01:10',
            ),
            10 => 
            array (
                'id' => 61,
                'nama' => 'Infection with koi herpesvirus',
            'organisme_penyebab' => 'Koi herpesvirus (KHV)',
                'singkatan' => NULL,
                'golongan' => 'Virus',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-05-11 04:01:10',
                'updated_at' => '2026-05-11 04:01:10',
            ),
            11 => 
            array (
                'id' => 62,
                'nama' => 'Infection with viral haemorrhagic septicaemia virus',
            'organisme_penyebab' => 'Viral   haemorrhagic septicemia virus (VHSV)',
                'singkatan' => NULL,
                'golongan' => 'Virus',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-05-11 04:01:10',
                'updated_at' => '2026-05-11 04:01:10',
            ),
            12 => 
            array (
                'id' => 63,
                'nama' => 'Infection with HPR-deleted or HPR0 infectious salmon anaemia virus',
            'organisme_penyebab' => 'Highly polymorphic region (HPR)-deleted ISAV (HPR- deleted ISAV)',
                'singkatan' => NULL,
                'golongan' => 'Virus',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-05-11 04:01:10',
                'updated_at' => '2026-05-11 04:01:10',
            ),
            13 => 
            array (
                'id' => 64,
                'nama' => 'Infection with salmonid alphavirus',
            'organisme_penyebab' => 'Salmonid alphavirus (SAV)',
                'singkatan' => NULL,
                'golongan' => 'Virus',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-05-11 04:01:10',
                'updated_at' => '2026-05-11 04:01:10',
            ),
            14 => 
            array (
                'id' => 65,
                'nama' => 'Infection with tilapia lake virus',
            'organisme_penyebab' => 'Tilapia lake virus (TiLV)',
                'singkatan' => NULL,
                'golongan' => 'Virus',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-05-11 04:01:10',
                'updated_at' => '2026-05-11 04:01:10',
            ),
            15 => 
            array (
                'id' => 66,
            'nama' => 'Enteric septicaemia   of catfish   (ESC)',
                'organisme_penyebab' => 'Edwardsiella   ictaluri',
                'singkatan' => NULL,
                'golongan' => 'Bakteri',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-05-11 04:01:10',
                'updated_at' => '2026-05-11 04:01:10',
            ),
            16 => 
            array (
                'id' => 67,
                'nama' => 'Furunculosis/Carp erytrodermatitis',
                'organisme_penyebab' => 'Aeromonas salmonicida',
                'singkatan' => NULL,
                'golongan' => 'Bakteri',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-05-11 04:01:10',
                'updated_at' => '2026-05-11 04:01:10',
            ),
            17 => 
            array (
                'id' => 68,
                'nama' => 'Streptococciasis',
                'organisme_penyebab' => 'Streptococcus iniae',
                'singkatan' => NULL,
                'golongan' => 'Bakteri',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-05-11 04:01:10',
                'updated_at' => '2026-05-11 04:01:10',
            ),
            18 => 
            array (
                'id' => 69,
                'nama' => 'Streptococciasis',
                'organisme_penyebab' => 'Streptococcus agalactiae',
                'singkatan' => NULL,
                'golongan' => 'Bakteri',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-05-11 04:01:10',
                'updated_at' => '2026-05-11 04:01:10',
            ),
            19 => 
            array (
                'id' => 70,
            'nama' => 'Enteric red mouth disease (ERM)',
                'organisme_penyebab' => 'Yersinia ruckeri',
                'singkatan' => NULL,
                'golongan' => 'Bakteri',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-05-11 04:01:10',
                'updated_at' => '2026-05-11 04:01:10',
            ),
            20 => 
            array (
                'id' => 71,
            'nama' => 'Bacterial kidney disease (BKD)',
                'organisme_penyebab' => 'Renibacterium salmoninarum',
                'singkatan' => NULL,
                'golongan' => 'Bakteri',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-05-11 04:01:10',
                'updated_at' => '2026-05-11 04:01:10',
            ),
            21 => 
            array (
                'id' => 72,
                'nama' => 'Infection with Gyrodactylus   salaris',
                'organisme_penyebab' => 'Gyrodactylus   salaris',
                'singkatan' => NULL,
                'golongan' => 'Parasit',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-05-11 04:01:10',
                'updated_at' => '2026-05-11 04:01:10',
            ),
            22 => 
            array (
                'id' => 73,
                'nama' => 'Whirling disease',
                'organisme_penyebab' => 'Myxobolus cerebralis',
                'singkatan' => NULL,
                'golongan' => 'Parasit',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-05-11 04:01:10',
                'updated_at' => '2026-05-11 04:01:10',
            ),
            23 => 
            array (
                'id' => 74,
                'nama' => 'Infection with Aphanomyces invadans
(epizootic ulcerative syndrome)',
                    'organisme_penyebab' => 'Aphanomyces   invadans',
                    'singkatan' => NULL,
                    'golongan' => 'Jamur',
                    'keterangan' => NULL,
                    'aktif' => 1,
                    'created_at' => '2026-05-11 04:01:10',
                    'updated_at' => '2026-05-11 04:01:10',
                ),
                24 => 
                array (
                    'id' => 75,
                    'nama' => 'Infection with infectious   hypodermal and haematopoietic necrosis virus',
                'organisme_penyebab' => 'Infectious hypodermal and haematopoietic necrosis   virus (IHHNV)',
                    'singkatan' => NULL,
                    'golongan' => 'Virus',
                    'keterangan' => NULL,
                    'aktif' => 1,
                    'created_at' => '2026-05-11 04:01:10',
                    'updated_at' => '2026-05-11 04:01:10',
                ),
                25 => 
                array (
                    'id' => 76,
                    'nama' => 'Infection with yellow head virus genotype 1',
                'organisme_penyebab' => 'Yellow head virus (YHV)',
                    'singkatan' => NULL,
                    'golongan' => 'Virus',
                    'keterangan' => NULL,
                    'aktif' => 1,
                    'created_at' => '2026-05-11 04:01:10',
                    'updated_at' => '2026-05-11 04:01:10',
                ),
                26 => 
                array (
                    'id' => 77,
                    'nama' => 'Infection with Taura syndrome virus',
                'organisme_penyebab' => 'Taura syndrome virus (TSV)',
                    'singkatan' => NULL,
                    'golongan' => 'Virus',
                    'keterangan' => NULL,
                    'aktif' => 1,
                    'created_at' => '2026-05-11 04:01:10',
                    'updated_at' => '2026-05-11 04:01:10',
                ),
                27 => 
                array (
                    'id' => 78,
                    'nama' => 'Infection with white spot syndrome virus',
                'organisme_penyebab' => 'White spot syndrome virus (WSSV)',
                    'singkatan' => NULL,
                    'golongan' => 'Virus',
                    'keterangan' => NULL,
                    'aktif' => 1,
                    'created_at' => '2026-05-11 04:01:10',
                    'updated_at' => '2026-05-11 04:01:10',
                ),
                28 => 
                array (
                    'id' => 79,
                'nama' => 'Infection with Macrobrachium  rosenbergii nodavirus (white tail disease)',
                'organisme_penyebab' => 'Macrobrachium rosenbergii nodavirus (MrNV), MrNV (primary) and extra small virus (XSV) (associate)',
                    'singkatan' => NULL,
                    'golongan' => 'Virus',
                    'keterangan' => NULL,
                    'aktif' => 1,
                    'created_at' => '2026-05-11 04:01:10',
                    'updated_at' => '2026-05-11 04:01:10',
                ),
                29 => 
                array (
                    'id' => 80,
                    'nama' => 'Infection with infectious   myonecrosis virus',
                'organisme_penyebab' => 'Infectious   myonecrosis   virus (IMNV)',
                    'singkatan' => NULL,
                    'golongan' => 'Virus',
                    'keterangan' => NULL,
                    'aktif' => 1,
                    'created_at' => '2026-05-11 04:01:10',
                    'updated_at' => '2026-05-11 04:01:10',
                ),
                30 => 
                array (
                    'id' => 81,
                'nama' => 'Viral covert mortality disease (VCMD) of shrimp atau Infection with covert mortality nodavirus',
                'organisme_penyebab' => 'Covert mortality nodavirus (CMNV)',
                    'singkatan' => NULL,
                    'golongan' => 'Virus',
                    'keterangan' => NULL,
                    'aktif' => 1,
                    'created_at' => '2026-05-11 04:01:10',
                    'updated_at' => '2026-05-11 04:01:10',
                ),
                31 => 
                array (
                    'id' => 82,
                    'nama' => 'Infection with decapod iridescent virus 1',
                'organisme_penyebab' => 'Decapod iridescent virus - 1 (DIV-1)',
                    'singkatan' => NULL,
                    'golongan' => 'Virus',
                    'keterangan' => NULL,
                    'aktif' => 1,
                    'created_at' => '2026-05-11 04:01:10',
                    'updated_at' => '2026-05-11 04:01:10',
                ),
                32 => 
                array (
                    'id' => 83,
                    'nama' => 'Acute hepatopancreatic necrosis disease',
                'organisme_penyebab' => 'Vibrio parahaemolyticus (Vp AHPND)',
                    'singkatan' => NULL,
                    'golongan' => 'Bakteri',
                    'keterangan' => NULL,
                    'aktif' => 1,
                    'created_at' => '2026-05-11 04:01:10',
                    'updated_at' => '2026-05-11 04:01:10',
                ),
                33 => 
                array (
                    'id' => 84,
                    'nama' => 'Infection with Hepatobacter penaei
(necrotising hepatopancreatitis)',
                        'organisme_penyebab' => 'Hepatobacter penaei',
                        'singkatan' => NULL,
                        'golongan' => 'Bakteri',
                        'keterangan' => NULL,
                        'aktif' => 1,
                        'created_at' => '2026-05-11 04:01:10',
                        'updated_at' => '2026-05-11 04:01:10',
                    ),
                    34 => 
                    array (
                        'id' => 85,
                        'nama' => 'Hepatopancreatic microsporidiosis caused by Enterocytozoon hepatopenaei',
                        'organisme_penyebab' => 'Enterocytozoon hepatopenaei',
                        'singkatan' => NULL,
                        'golongan' => 'Parasit',
                        'keterangan' => NULL,
                        'aktif' => 1,
                        'created_at' => '2026-05-11 04:01:10',
                        'updated_at' => '2026-05-11 04:01:10',
                    ),
                    35 => 
                    array (
                        'id' => 86,
                        'nama' => 'Infection with Aphanomyces   astaci
(crayfish plague)',
                            'organisme_penyebab' => 'Aphanomyces   astaci',
                            'singkatan' => NULL,
                            'golongan' => 'Jamur',
                            'keterangan' => NULL,
                            'aktif' => 1,
                            'created_at' => '2026-05-11 04:01:10',
                            'updated_at' => '2026-05-11 04:01:10',
                        ),
                        36 => 
                        array (
                            'id' => 87,
                            'nama' => 'Infection with abalone herpesvirus',
                        'organisme_penyebab' => 'Abalone herpesvirus   (AbHV)',
                            'singkatan' => NULL,
                            'golongan' => 'Virus',
                            'keterangan' => NULL,
                            'aktif' => 1,
                            'created_at' => '2026-05-11 04:01:10',
                            'updated_at' => '2026-05-11 04:01:10',
                        ),
                        37 => 
                        array (
                            'id' => 88,
                            'nama' => 'Infection   with ostreid herpesvirus 1 microvariants',
                        'organisme_penyebab' => 'Osterid herpersvirus-1 microvariants (OsHV-1 microvariants)',
                            'singkatan' => NULL,
                            'golongan' => 'Virus',
                            'keterangan' => NULL,
                            'aktif' => 1,
                            'created_at' => '2026-05-11 04:01:10',
                            'updated_at' => '2026-05-11 04:01:10',
                        ),
                        38 => 
                        array (
                            'id' => 89,
                            'nama' => 'Infections with Xenohaliotis californiensis',
                            'organisme_penyebab' => 'Xenohaliotis   californiensis',
                            'singkatan' => NULL,
                            'golongan' => 'Bakteri',
                            'keterangan' => NULL,
                            'aktif' => 1,
                            'created_at' => '2026-05-11 04:01:10',
                            'updated_at' => '2026-05-11 04:01:10',
                        ),
                        39 => 
                        array (
                            'id' => 90,
                            'nama' => 'Infection with Bonamia exitiosa',
                            'organisme_penyebab' => 'Bonamia exitiosa',
                            'singkatan' => NULL,
                            'golongan' => 'Parasit',
                            'keterangan' => NULL,
                            'aktif' => 1,
                            'created_at' => '2026-05-11 04:01:10',
                            'updated_at' => '2026-05-11 04:01:10',
                        ),
                        40 => 
                        array (
                            'id' => 91,
                            'nama' => 'Infection with Bonamia ostreae',
                            'organisme_penyebab' => 'Bonamia ostreae',
                            'singkatan' => NULL,
                            'golongan' => 'Parasit',
                            'keterangan' => NULL,
                            'aktif' => 1,
                            'created_at' => '2026-05-11 04:01:10',
                            'updated_at' => '2026-05-11 04:01:10',
                        ),
                        41 => 
                        array (
                            'id' => 92,
                            'nama' => 'Infection with Marteilia refringens',
                            'organisme_penyebab' => 'Marteilia refringens',
                            'singkatan' => NULL,
                            'golongan' => 'Parasit',
                            'keterangan' => NULL,
                            'aktif' => 1,
                            'created_at' => '2026-05-11 04:01:10',
                            'updated_at' => '2026-05-11 04:01:10',
                        ),
                        42 => 
                        array (
                            'id' => 93,
                            'nama' => 'Infection with Perkinsus olseni',
                            'organisme_penyebab' => 'Perkinsus olseni',
                            'singkatan' => NULL,
                            'golongan' => 'Parasit',
                            'keterangan' => NULL,
                            'aktif' => 1,
                            'created_at' => '2026-05-11 04:01:10',
                            'updated_at' => '2026-05-11 04:01:10',
                        ),
                        43 => 
                        array (
                            'id' => 94,
                            'nama' => 'Infection with Perkinsus marinus',
                            'organisme_penyebab' => 'Perkinsus   marinus',
                            'singkatan' => NULL,
                            'golongan' => 'Parasit',
                            'keterangan' => NULL,
                            'aktif' => 1,
                            'created_at' => '2026-05-11 04:01:10',
                            'updated_at' => '2026-05-11 04:01:10',
                        ),
                        44 => 
                        array (
                            'id' => 95,
                        'nama' => 'SSO disease (Seaside organism)',
                            'organisme_penyebab' => 'Haplosporidium costale',
                            'singkatan' => NULL,
                            'golongan' => 'Parasit',
                            'keterangan' => NULL,
                            'aktif' => 1,
                            'created_at' => '2026-05-11 04:01:10',
                            'updated_at' => '2026-05-11 04:01:10',
                        ),
                        45 => 
                        array (
                            'id' => 96,
                        'nama' => 'MSX disease (Multinucleate sphere X)',
                            'organisme_penyebab' => 'Haplosporidium nelsoni',
                            'singkatan' => NULL,
                            'golongan' => 'Parasit',
                            'keterangan' => NULL,
                            'aktif' => 1,
                            'created_at' => '2026-05-11 04:01:10',
                            'updated_at' => '2026-05-11 04:01:10',
                        ),
                        46 => 
                        array (
                            'id' => 97,
                            'nama' => 'Infection with Ranavirus species',
                        'organisme_penyebab' => 'Bohle iridovirus (BIV)',
                            'singkatan' => NULL,
                            'golongan' => 'Virus',
                            'keterangan' => NULL,
                            'aktif' => 1,
                            'created_at' => '2026-05-11 04:01:10',
                            'updated_at' => '2026-05-11 04:01:10',
                        ),
                        47 => 
                        array (
                            'id' => 98,
                            'nama' => 'Infection with Ranavirus species',
                        'organisme_penyebab' => 'Ambystoma tigrinum virus (ATV) Syn. Regina ranavirus',
                            'singkatan' => NULL,
                            'golongan' => 'Virus',
                            'keterangan' => NULL,
                            'aktif' => 1,
                            'created_at' => '2026-05-11 04:01:10',
                            'updated_at' => '2026-05-11 04:01:10',
                        ),
                        48 => 
                        array (
                            'id' => 99,
                            'nama' => 'Infection with Batrachochytrium dendrobatidis',
                            'organisme_penyebab' => 'Batrachochytrium dendrobatidis',
                            'singkatan' => NULL,
                            'golongan' => 'Jamur',
                            'keterangan' => NULL,
                            'aktif' => 1,
                            'created_at' => '2026-05-11 04:01:10',
                            'updated_at' => '2026-05-11 04:01:10',
                        ),
                        49 => 
                        array (
                            'id' => 100,
                            'nama' => 'Infection with Batrachochytrium salamandrivorans',
                            'organisme_penyebab' => 'Batrachochytrium salamandrivorans',
                            'singkatan' => NULL,
                            'golongan' => 'Jamur',
                            'keterangan' => NULL,
                            'aktif' => 1,
                            'created_at' => '2026-05-11 04:01:10',
                            'updated_at' => '2026-05-11 04:01:10',
                        ),
                    ));
        
        
    }
}