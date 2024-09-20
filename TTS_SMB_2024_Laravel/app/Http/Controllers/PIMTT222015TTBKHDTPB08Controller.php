<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\IjReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Traits\Common;
use Module\Report\Traits\Operating;

class PIMTT222015TTBKHDTPB08Controller extends Controller
{
    public function PIMTT222015TTBKHDTPB05(Request $request)
    {
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];

        // Post Form Filter
        $filter = $request->post('filter');
        $fromDate = (isset($filter['FromDate'])) ? $filter['FromDate'] : date('d/m/Y');
        $toDate = (isset($filter['ToDate'])) ? $filter['ToDate'] : date('d/m/Y');

        $fromDateF = date('Y-m-d', strtotime(str_replace('/', '-', $fromDate)));
        $toDateF = date('Y-m-d', strtotime(str_replace('/', '-', $toDate)));

        Log::alert($fromDateF);

        $toMonth = date('m', strtotime($toDateF));
        $toQuarter = (int) ((($toMonth - 1) / 3) + 1);
        if ($toQuarter == 1) {
            $toQuarterMonth = '03';
        } elseif ($toQuarter == 2) {
            $toQuarterMonth = '06';
        } elseif ($toQuarter == 3) {
            $toQuarterMonth = '09';
        } elseif ($toQuarter == 4) {
            $toQuarterMonth = '12';
        }
        $year = date('Y', strtotime(str_replace('/', '-', $fromDate)));

        $wheredate = " AND A.PostDate >= '$fromDateF' and A.PostDate <= '$toDateF' ";
        $uomName = isset($filter['UomName']) ? $filter['UomName'] : 'Đồng';

        $filterClause = '';
        if (isset($filter['ProvinceID'])) {
            $ProvinceID = $filter['ProvinceID'];
            $filterClause .= " AND e.ProvinceID = '$ProvinceID'";
        }

        if (isset($filter['ProvinceName'])) {
            $ProvinceName = $filter['ProvinceName'];
            $filterClause .= " AND e.ProvinceName = '$ProvinceName'";
        }
        if (isset($filter['ProvinceNo'])) {
            $ProvinceNo = $filter['ProvinceNo'];
            $filterClause .= " AND e.ProvinceNo = '$ProvinceNo'";
        }

        if (isset($filter['ProjectID'])) {
            $ProjectID = $filter['ProjectID'];
            $filterClause .= " AND e.ProjectID = '$ProjectID'";
        }

        if (isset($filter['ProjectNo'])) {
            $ProjectNo = $filter['ProjectNo'];
            $filterClause .= " AND e.ProjectNo = '$ProjectNo'";
        }

        if (isset($filter['ProjectName'])) {
            $ProjectName = $filter['ProjectName'];
            $filterClause .= " AND e.ProjectName = '$ProjectName'";
        }
        // End Post Form Filter

        // Create Temp Table
        $createTable = 'CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
            `ItemID` varchar(255) NOT NULL DEFAULT UUID(),
            `ProjectID` varchar(255) DEFAULT NULL,
            `STT` varchar(255) DEFAULT NULL,
            `Level` int(10) DEFAULT NULL,
            `Sumup` int(10) DEFAULT 1,
            `Project` varchar(255) DEFAULT NULL,
            `ItemName` varchar(255) DEFAULT NULL,
            `I1` double(20,4) DEFAULT 0,
            `I2` double(20,4) DEFAULT 0,
            `I3` double(20,4) DEFAULT 0,
            `I4` double(20,4) DEFAULT 0,
            `I5` double(20,4) DEFAULT 0,
            `I6` double(20,4) DEFAULT 0,
            `I7` double(20,4) DEFAULT 0,
            `ParentID` varchar(255) DEFAULT NULL,
            `FontWeight` tinyint(1) DEFAULT 0,
            `Center` tinyint(1) DEFAULT 0,
            `Italic` tinyint(1) DEFAULT 0,
            `Norder` int(10) DEFAULT 0,
            `Detail` int(10) DEFAULT 1,
            PRIMARY KEY (`ItemID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci';
        DB::statement($createTable);
        // End Create Temp Table

        // Create Temp Count Table Statement
        $createTempCountTable = "CREATE TEMPORARY TABLE temp_counts AS
            (
            SELECT DISTINCT
            d.`a_CateValue`, d.`b_CateValue`, c.`StatusID`, c.`StatusValue`, A.`ProjectID`
            FROM (
            SELECT DISTINCT a.`ProjectID`, a.`CateValue` AS a_CateValue, b.`CateValue` AS b_CateValue
            FROM project_cate AS a
            INNER JOIN project_cate AS b ON a.`ProjectID` = b.`ProjectID`
            WHERE
            a.`CateNo` IN ('0243') AND b.`CateNo` IN ('025') AND a.`CateValue` IN ('1', '2') AND b.`CateValue` IN ('1', '2', '3')
            ) AS d
                INNER JOIN glm_gl_books AS A ON A.ProjectID = d.ProjectID
                INNER JOIN project_status_item AS c ON A.`ProjectID` = c.`ProjectID`
            WHERE 1=1 $filterClause
            );";
        // End Create Temp Count Table Statement

        DB::statement($createTempCountTable);

        // Create Capital Count 1 Table Statement
        $createCapitalTable1 = "
                        CREATE TEMPORARY TABLE IF NOT EXISTS capital_counts1 AS (
                            SELECT
                              SUM(CASE WHEN PostType = 2 THEN S ELSE 0 END) AS A1,
                              SUM(CASE WHEN PostType = 1 THEN I ELSE 0 END) AS A2,
                              isAdjustTrans, PostDate, a_CateValue, c_CateValue
                            FROM (
                              SELECT
                                SUM(`LCAmount`) AS I, SUM(`LCAmount`) * (-1) AS S, PostType, isAdjustTrans, PostDate, a_CateValue, c_CateValue
                              FROM (
                                SELECT DISTINCT
                                  B.`LCAmount`, B.PostType, B.isAdjustTrans, B.PostDate, B.ProjectID, A.CateValue AS a_CateValue, C.CateValue AS c_CateValue
                                FROM `project_cate` AS A
                                INNER JOIN `project_cate` AS C ON A.`ProjectID` = C.`ProjectID`
                                INNER JOIN `glm_gl_books` AS B ON A.`ProjectID` = B.`ProjectID`
                                WHERE A.CateNo = '0243' AND A.CateValue IN ('1', '2') AND C.CateNo = '025' AND C.CateValue IN ('1', '2', '3') AND B.AccountNo IN ('0412', '0413')
                              ) AS CH
                              GROUP BY CH.PostType, CH.isAdjustTrans, CH.PostDate, CH.a_CateValue, CH.c_CateValue
                            ) AS E
                            GROUP BY E.isAdjustTrans, E.PostDate, E.a_CateValue, E.c_CateValue
                        );                   
                                  ";
        // End Create Capital Count 1 Table Statement
        DB::statement($createCapitalTable1);

        // Create Capital Count 2 Table Statement
        $createCapitalTable2 = "
            CREATE TEMPORARY TABLE IF NOT EXISTS capital_counts2 AS (
                  SELECT
                    SUM(CASE WHEN PostType = 2 THEN S ELSE 0 END) AS A1,
                    SUM(CASE WHEN PostType = 1 THEN I ELSE 0 END) AS A2,
                    PostDate, a_CateValue, c_CateValue, d_CateNo
                  FROM (
                    SELECT
                      SUM(`LCAmount`) AS I, SUM(`LCAmount`) * (-1) AS S, PostType, PostDate, a_CateValue, c_CateValue, d_CateNo
                    FROM (
                      SELECT DISTINCT
                        B.`LCAmount`, B.PostType, B.PostDate, B.ProjectID, A.CateValue AS a_CateValue, C.CateValue AS c_CateValue, D.CateNo AS d_CateNo
                      FROM `project_cate` AS A
                      INNER JOIN `project_cate` AS C ON A.`ProjectID` = C.`ProjectID`
                      INNER JOIN `glm_gl_books` AS B ON A.`ProjectID` = B.`ProjectID`
                      INNER JOIN `capital_cate` AS D ON B.CapitalID = D.CapitalID
                      WHERE A.CateNo = '0243' AND A.CateValue IN ('1', '2') AND C.CateNo = '025' AND C.CateValue IN ('1', '2', '3') 
                      AND B.AccountNo IN ('097') AND D.CateNo IN ('2122', '2123', '2124')
                    ) AS CH
                    GROUP BY CH.PostType, CH.PostDate, CH.a_CateValue, CH.c_CateValue, CH.d_CateNo
                  ) AS E
                  GROUP BY E.PostDate, E.a_CateValue, E.c_CateValue, E.d_CateNo
            );
                             ";
        // End Create Capital Count 2 Table Statement
        DB::statement($createCapitalTable2);

        $master = [
            'ReportName' => 'Tình hình thực hiện giám sát đầu tư các dự án sử dụng các nguồn vốn khác',
            'UomName' => 'Đơn vị tính: ' . $uomName,
        ];

        // Pass data from sample database
        $sourceData = DB::table('tt22pb05___hoang')->select('STT', 'ItemID', 'ItemName', 'ParentID')->get();
        foreach ($sourceData as $data) {
            DB::table('table_tmp')->insert([
                'STT' => $data->STT,
                'ItemID' => $data->ItemID,
                'ItemName' => $data->ItemName,
                'ParentID' => $data->ParentID,
            ]);
        }
        // End pass data

        // Insert for each row
        self::insert1('A#01', 36, [2]);
        self::insert1('A#02', 36, [3]);
        self::insert1('A#03', 38, [2]);
        self::insert1('A#04', 38, [3]);
        self::insert1('A#05#01', 38, [2, 3]);
        self::insert1('A#05#02', 38, [4]);
        self::insert1('A#08', 24, [4]);
        self::insert1('A#09', 27, [2, 3, 4]);
        self::insert1('A#10', 27, [6]);
        self::insert1('A#11', 27, [7]);
        self::insert1('A#12', 27, [8]);
        self::insert1('A#13', 27, [9]);
        self::insert1('A#14', 34, [3, 4]);
        self::insert1('A#15', 35, [], [1]);
        self::insert1('A#15#01', 35, [6], [1]);

        self::insertRow6('A#06#01', $toDateF, '', 2);
        self::insertRow6('A#06#02#01', $toDateF, $fromDateF, 0);
        self::insertRow6('A#06#02#02', $toDateF, $fromDateF, 1);

        self::insertRow7('A#07#01', $toDateF, '');

        self::insertRow7('A#07#02#01', $toDateF, $fromDateF, 2122);
        self::insertRow7('A#07#02#02', $toDateF, $fromDateF, 2123);
        self::insertRow7('A#07#02#03', $toDateF, $fromDateF, 2124);

        self::sumParentRow('A#05');
        self::sumParentRow('A#06#02');
        self::sumParentRow('A#06');
        self::sumParentRow('A#07#02');
        self::sumParentRow('A#07');
        // End insert for each row

        $detail = DB::table('table_tmp')->get();

        $response = [
            'master' => $master,
            'detail' => $detail
        ];
        $json['data'] = $response;
        return json_encode($json);
    }

    // a-0243 b-025
    public function insert1($ItemID, $statusId, array $statusValues = [], array $excludeValues = [])
    {
        // Convert the status values and exclude values arrays to comma-separated strings
        $statusValuesString = implode("', '", $statusValues);
        $excludeValuesString = implode("', '", $excludeValues);

        // Construct the inclusion and exclusion clauses
        $includeClause = !empty($statusValues) ? "e.`StatusValue` IN ('$statusValuesString')" : '1=1';
        $excludeClause = !empty($excludeValues) ? "AND e.`StatusValue` NOT IN ('$excludeValuesString')" : '';

        DB::enableQueryLog();
        // Define the select query
        $sql = "
                SELECT
            COUNT(DISTINCT CASE WHEN e.`StatusID` = $statusId AND $includeClause AND e.a_CateValue = '2' AND e.b_CateValue = '1' $excludeClause THEN e.ProjectID END) AS I2,
            COUNT(DISTINCT CASE WHEN e.`StatusID` = $statusId AND $includeClause AND e.a_CateValue = '2' AND e.b_CateValue = '2' $excludeClause THEN e.ProjectID END) AS I3,
            COUNT(DISTINCT CASE WHEN e.`StatusID` = $statusId AND $includeClause AND e.a_CateValue = '2' AND e.b_CateValue = '3' $excludeClause THEN e.ProjectID END) AS I4,
            COUNT(DISTINCT CASE WHEN e.`StatusID` = $statusId AND $includeClause AND e.a_CateValue = '1' AND e.b_CateValue = '1' $excludeClause THEN e.ProjectID END) AS I5,
            COUNT(DISTINCT CASE WHEN e.`StatusID` = $statusId AND $includeClause AND e.a_CateValue = '1' AND e.b_CateValue = '2' $excludeClause THEN e.ProjectID END) AS I6,
            COUNT(DISTINCT CASE WHEN e.`StatusID` = $statusId AND $includeClause AND e.a_CateValue = '1' AND e.b_CateValue = '3' $excludeClause THEN e.ProjectID END) AS I7
            FROM `temp_counts` AS e;
                ";

        // Execute the select query
        $results = DB::select($sql);

        if (!empty($results)) {
            $counts = (array) $results[0];  // Convert stdClass object to an associative array
            // Log::alert($counts);

            // Define and execute the update query
            DB::update('UPDATE `table_tmp` SET I2 = :I2, I3 = :I3, I4 = :I4, I5 = :I5, I6 = :I6, I7 = :I7 WHERE `ItemID` = :ItemID', [
                'I2' => $counts['I2'], 'I3' => $counts['I3'], 'I4' => $counts['I4'],
                'I5' => $counts['I5'], 'I6' => $counts['I6'], 'I7' => $counts['I7'],
                'ItemID' => $ItemID
            ]);

            $sumUpComlumn = DB::table('table_tmp')
                ->where('ItemID', $ItemID)
                ->update(['I1' => array_sum($counts)]);
            // Log::info(DB::getQueryLog());
        }
    }

    public function sumParentRow($ItemID)
    {
        $sql = " Select SUM(I2) AS I2, SUM(I3) AS I3, SUM(I4) AS I4, SUM(I5) AS I5, SUM(I6) AS I6, SUM(I7) AS I7 FROM `table_tmp` WHERE ParentID = '$ItemID' ";
        $results = DB::select($sql);
        $counts = (array) $results[0];  // Convert stdClass object to an associative array

        // Define and execute the update query
        DB::update('UPDATE `table_tmp` SET I2 = :I2, I3 = :I3, I4 = :I4, I5 = :I5, I6 = :I6, I7 = :I7 WHERE `ItemID` = :ItemID', [
            'I2' => $counts['I2'], 'I3' => $counts['I3'], 'I4' => $counts['I4'],
            'I5' => $counts['I5'], 'I6' => $counts['I6'], 'I7' => $counts['I7'],
            'ItemID' => $ItemID
        ]);

        $sumUpComlumn = DB::table('table_tmp')
            ->where('ItemID', $ItemID)
            ->update(['I1' => array_sum($counts)]);
    }

    // is adjust trans
    // check post date

    public function insertRow6($ItemID, $toDate, $fromDate, $isAdjustTrans)
    {
        $isAdjustTransClause = '';
        $dateClause = '';
        if ($fromDate && $toDate) {
            $dateClause = "AND PostDate >= '$fromDate' AND PostDate <= '$toDate'";
        } elseif ($fromDate) {
            $dateClause = "AND PostDate >= '$fromDate'";
        } elseif ($toDate) {
            $dateClause = "AND PostDate <= '$toDate'";
        }

        switch ($isAdjustTrans) {
            case '0':
                $isAdjustTransClause = "AND isAdjustTrans NOT IN ('1')";
                break;
            case '1':
                $isAdjustTransClause = "AND isAdjustTrans IN ('1')";
                break;
            default:
                $isAdjustTransClause = '';
                break;
        }
        $sql = "SELECT

            SUM(CASE WHEN e.a_CateValue IN ('2') AND e.c_CateValue IN ('1') $isAdjustTransClause $dateClause THEN A1 + A2 ELSE 0 END) AS I2,
            SUM(CASE WHEN e.a_CateValue IN ('2') AND e.c_CateValue IN ('2') $isAdjustTransClause $dateClause THEN A1 + A2 ELSE 0 END) AS I3,
            SUM(CASE WHEN e.a_CateValue IN ('2') AND e.c_CateValue IN ('3') $isAdjustTransClause $dateClause THEN A1 + A2 ELSE 0 END) AS I4,
            SUM(CASE WHEN e.a_CateValue IN ('1') AND e.c_CateValue IN ('1') $isAdjustTransClause $dateClause THEN A1 + A2 ELSE 0 END) AS I5,
            SUM(CASE WHEN e.a_CateValue IN ('1') AND e.c_CateValue IN ('2') $isAdjustTransClause $dateClause THEN A1 + A2 ELSE 0 END) AS I6,
            SUM(CASE WHEN e.a_CateValue IN ('1') AND e.c_CateValue IN ('3') $isAdjustTransClause $dateClause THEN A1 + A2 ELSE 0 END) AS I7

                FROM capital_counts1 as e;";

        $results = DB::select($sql);
        if (!empty($results)) {
            $counts = (array) $results[0];  // Convert stdClass object to an associative array

            // Define and execute the update query
            DB::update('UPDATE `table_tmp` SET I2 = :I2, I3 = :I3, I4 = :I4, I5 = :I5, I6 = :I6, I7 = :I7 WHERE `ItemID` = :ItemID', [
                'I2' => $counts['I2'], 'I3' => $counts['I3'], 'I4' => $counts['I4'],
                'I5' => $counts['I5'], 'I6' => $counts['I6'], 'I7' => $counts['I7'],
                'ItemID' => $ItemID
            ]);

            $sumUpComlumn = DB::table('table_tmp')
                ->where('ItemID', $ItemID)
                ->update(['I1' => array_sum($counts)]);
        }
    }

    public function insertRow7($ItemID, $toDate, $fromDate, $CateNo = null)
    {
        $cateNoClause = '';
        $CateNo ? $cateNoClause = "AND e.d_CateNo IN('$CateNo')" : '';
        $dateClause = '';
        if ($fromDate && $toDate) {
            $dateClause = "AND PostDate >= '$fromDate' AND PostDate <= '$toDate'";
        } elseif (!$toDate && $fromDate) {
            $dateClause = "AND PostDate >= '$fromDate'";
        } elseif (!$fromDate && $toDate) {
            $dateClause = "AND PostDate <= '$toDate'";
        }
        $sql = "SELECT
                SUM(CASE WHEN e.a_CateValue IN ('2') AND e.c_CateValue IN ('1') $cateNoClause $dateClause THEN A1 + A2 ELSE 0 END) AS I2,
                SUM(CASE WHEN e.a_CateValue IN ('2') AND e.c_CateValue IN ('2') $cateNoClause $dateClause THEN A1 + A2 ELSE 0 END) AS I3,
                SUM(CASE WHEN e.a_CateValue IN ('2') AND e.c_CateValue IN ('3') $cateNoClause $dateClause THEN A1 + A2 ELSE 0 END) AS I4,
                SUM(CASE WHEN e.a_CateValue IN ('1') AND e.c_CateValue IN ('1') $cateNoClause $dateClause THEN A1 + A2 ELSE 0 END) AS I5,
                SUM(CASE WHEN e.a_CateValue IN ('1') AND e.c_CateValue IN ('2') $cateNoClause $dateClause THEN A1 + A2 ELSE 0 END) AS I6,
                SUM(CASE WHEN e.a_CateValue IN ('1') AND e.c_CateValue IN ('3') $cateNoClause $dateClause THEN A1 + A2 ELSE 0 END) AS I7

            FROM capital_counts2 as e; ";

        $results = DB::select($sql);
        if (!empty($results)) {
            $counts = (array) $results[0];  // Convert stdClass object to an associative array

            // Define and execute the update query
            DB::update('UPDATE `table_tmp` SET I2 = :I2, I3 = :I3, I4 = :I4, I5 = :I5, I6 = :I6, I7 = :I7 WHERE `ItemID` = :ItemID', [
                'I2' => $counts['I2'], 'I3' => $counts['I3'], 'I4' => $counts['I4'],
                'I5' => $counts['I5'], 'I6' => $counts['I6'], 'I7' => $counts['I7'],
                'ItemID' => $ItemID
            ]);

            $sumUpComlumn = DB::table('table_tmp')
                ->where('ItemID', $ItemID)
                ->update(['I1' => array_sum($counts)]);
        }
    }
}
