<?php
error_reporting(0);
global $wpdb;
$table_name = $wpdb->prefix . 'ebay_listings';
$select_query =  $wpdb->get_results("SELECT * FROM $table_name LIMIT 1");
$number = 0;
if(isset($_GET['delete_data'])){
    $id = $_GET['delete_data'];
    $wpdb->delete( $table_name, array( 'id' => $id ) );
}
if (empty($select_query)) {
    return;
} else {

//    $check1 =  add_query_arg('delete_data' , $select_query[0]->is);
   $check1 = add_query_arg( array(
    'delete_data' => $select_query[0]->id,
   ));
?>

<h3><a href="<?php echo esc_url($check1) ?>">Delete All</a></h3>
<h3><a href="javascript:void(0)" class='insert_new_post' data-addID='<?php echo $addID[0][0] ?>'>Insert In Post</a></h3>
    <table class="widefat fixed" cellspacing="0">
        <tr>
            <th>Title</th>
            <th>Short Description</th>
            <th>Date</th>
            <th>Add Id</th>
            <th>Image URL</th>
            <th>Location</th>
            <th>List Items</th>
            <th>Long Description</th>

        </tr>
        <?php

        foreach ($select_query as $sql) {
            $title[] = json_decode($sql->title);
            $desc[] = json_decode($sql->description);
            $date[] = json_decode($sql->date);
            $addID[] = json_decode($sql->add_id);
            $image_URL[] = json_decode($sql->image_URL);
            $location[] = json_decode($sql->location);
            $list_items[] = json_decode($sql->list_items);
            $long_desctiption[] = json_decode($sql->long_desctiption);
        }

        ?>
        <tr class='tb1'>
            <td class='tb1-title1'><?php echo $title[0][0] ?></td>
            <td class='tb1-desc1'><?php echo $desc[0][0] ?></td>
            <td class='tb1-date1'><?php echo $date[0][0] ?></td>
            <td class='tb1-addId1'><?php echo $addID[0][0] ?></td>
            <td class='tb1-image_ur1'><?php echo $image_URL[0][0] ?></td>
            <td class='tb1-location1'><?php echo $location[0][0] ?></td>
            <td class='tb1-list_items1'><?php echo $list_items[0][0] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][0] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][1] ?></td>
            <td><?php echo $desc[0][1] ?></td>
            <td><?php echo $date[0][1] ?></td>
            <td><?php echo $addID[0][1] ?></td>
            <td><?php echo $image_URL[0][1] ?></td>
            <td><?php echo $location[0][1] ?></td>
            <td><?php echo $list_items[0][1] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][1] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][2] ?></td>
            <td><?php echo $desc[0][2] ?></td>
            <td><?php echo $date[0][2] ?></td>
            <td><?php echo $addID[0][2] ?></td>
            <td><?php echo $image_URL[0][2] ?></td>
            <td><?php echo $location[0][2] ?></td>
            <td><?php echo $list_items[0][2] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][2] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][3] ?></td>
            <td><?php echo $desc[0][3] ?></td>
            <td><?php echo $date[0][3] ?></td>
            <td><?php echo $addID[0][3] ?></td>
            <td><?php echo $image_URL[0][3] ?></td>
            <td><?php echo $location[0][3] ?></td>
            <td><?php echo $list_items[0][3] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][3] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][4] ?></td>
            <td><?php echo $desc[0][4] ?></td>
            <td><?php echo $date[0][4] ?></td>
            <td><?php echo $addID[0][4] ?></td>
            <td><?php echo $image_URL[0][4] ?></td>
            <td><?php echo $location[0][4] ?></td>
            <td><?php echo $list_items[0][4] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][4] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][5] ?></td>
            <td><?php echo $desc[0][5] ?></td>
            <td><?php echo $date[0][5] ?></td>
            <td><?php echo $addID[0][5] ?></td>
            <td><?php echo $image_URL[0][5] ?></td>
            <td><?php echo $location[0][5] ?></td>
            <td><?php echo $list_items[0][5] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][5] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][6] ?></td>
            <td><?php echo $desc[0][6] ?></td>
            <td><?php echo $date[0][6] ?></td>
            <td><?php echo $addID[0][6] ?></td>
            <td><?php echo $image_URL[0][6] ?></td>
            <td><?php echo $location[0][6] ?></td>
            <td><?php echo $list_items[0][6] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][6] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][7] ?></td>
            <td><?php echo $desc[0][7] ?></td>
            <td><?php echo $date[0][7] ?></td>
            <td><?php echo $addID[0][7] ?></td>
            <td><?php echo $image_URL[0][7] ?></td>
            <td><?php echo $location[0][7] ?></td>
            <td><?php echo $list_items[0][7] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][7] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][8] ?></td>
            <td><?php echo $desc[0][8] ?></td>
            <td><?php echo $date[0][8] ?></td>
            <td><?php echo $addID[0][8] ?></td>
            <td><?php echo $image_URL[0][8] ?></td>
            <td><?php echo $location[0][8] ?></td>
            <td><?php echo $list_items[0][8] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][8] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][9] ?></td>
            <td><?php echo $desc[0][9] ?></td>
            <td><?php echo $date[0][9] ?></td>
            <td><?php echo $addID[0][9] ?></td>
            <td><?php echo $image_URL[0][9] ?></td>
            <td><?php echo $location[0][9] ?></td>
            <td><?php echo $list_items[0][9] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][9] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][10] ?></td>
            <td><?php echo $desc[0][10] ?></td>
            <td><?php echo $date[0][10] ?></td>
            <td><?php echo $addID[0][10] ?></td>
            <td><?php echo $image_URL[0][10] ?></td>
            <td><?php echo $location[0][10] ?></td>
            <td><?php echo $list_items[0][10] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][10] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][11] ?></td>
            <td><?php echo $desc[0][11] ?></td>
            <td><?php echo $date[0][11] ?></td>
            <td><?php echo $addID[0][11] ?></td>
            <td><?php echo $image_URL[0][11] ?></td>
            <td><?php echo $location[0][11] ?></td>
            <td><?php echo $list_items[0][11] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][11] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][12] ?></td>
            <td><?php echo $desc[0][12] ?></td>
            <td><?php echo $date[0][12] ?></td>
            <td><?php echo $addID[0][12] ?></td>
            <td><?php echo $image_URL[0][12] ?></td>
            <td><?php echo $location[0][12] ?></td>
            <td><?php echo $list_items[0][12] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][12] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][13] ?></td>
            <td><?php echo $desc[0][13] ?></td>
            <td><?php echo $date[0][13] ?></td>
            <td><?php echo $addID[0][13] ?></td>
            <td><?php echo $image_URL[0][13] ?></td>
            <td><?php echo $location[0][13] ?></td>
            <td><?php echo $list_items[0][13] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][13] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][14] ?></td>
            <td><?php echo $desc[0][14] ?></td>
            <td><?php echo $date[0][14] ?></td>
            <td><?php echo $addID[0][14] ?></td>
            <td><?php echo $image_URL[0][14] ?></td>
            <td><?php echo $location[0][14] ?></td>
            <td><?php echo $list_items[0][14] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][14] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][15] ?></td>
            <td><?php echo $desc[0][15] ?></td>
            <td><?php echo $date[0][15] ?></td>
            <td><?php echo $addID[0][15] ?></td>
            <td><?php echo $image_URL[0][15] ?></td>
            <td><?php echo $location[0][15] ?></td>
            <td><?php echo $list_items[0][15] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][15] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][16] ?></td>
            <td><?php echo $desc[0][16] ?></td>
            <td><?php echo $date[0][16] ?></td>
            <td><?php echo $addID[0][16] ?></td>
            <td><?php echo $image_URL[0][16] ?></td>
            <td><?php echo $location[0][16] ?></td>
            <td><?php echo $list_items[0][16] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][16] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][17] ?></td>
            <td><?php echo $desc[0][17] ?></td>
            <td><?php echo $date[0][17] ?></td>
            <td><?php echo $addID[0][17] ?></td>
            <td><?php echo $image_URL[0][17] ?></td>
            <td><?php echo $location[0][17] ?></td>
            <td><?php echo $list_items[0][17] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][17] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][18] ?></td>
            <td><?php echo $desc[0][18] ?></td>
            <td><?php echo $date[0][18] ?></td>
            <td><?php echo $addID[0][18] ?></td>
            <td><?php echo $image_URL[0][18] ?></td>
            <td><?php echo $location[0][18] ?></td>
            <td><?php echo $list_items[0][18] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][18] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][19] ?></td>
            <td><?php echo $desc[0][19] ?></td>
            <td><?php echo $date[0][19] ?></td>
            <td><?php echo $addID[0][19] ?></td>
            <td><?php echo $image_URL[0][19] ?></td>
            <td><?php echo $location[0][19] ?></td>
            <td><?php echo $list_items[0][19] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][19] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][20] ?></td>
            <td><?php echo $desc[0][20] ?></td>
            <td><?php echo $date[0][20] ?></td>
            <td><?php echo $addID[0][20] ?></td>
            <td><?php echo $image_URL[0][20] ?></td>
            <td><?php echo $location[0][20] ?></td>
            <td><?php echo $list_items[0][20] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][20] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][21] ?></td>
            <td><?php echo $desc[0][21] ?></td>
            <td><?php echo $date[0][21] ?></td>
            <td><?php echo $addID[0][21] ?></td>
            <td><?php echo $image_URL[0][21] ?></td>
            <td><?php echo $location[0][21] ?></td>
            <td><?php echo $list_items[0][21] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][21] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][22] ?></td>
            <td><?php echo $desc[0][22] ?></td>
            <td><?php echo $date[0][22] ?></td>
            <td><?php echo $addID[0][22] ?></td>
            <td><?php echo $image_URL[0][22] ?></td>
            <td><?php echo $location[0][22] ?></td>
            <td><?php echo $list_items[0][22] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][22] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][23] ?></td>
            <td><?php echo $desc[0][23] ?></td>
            <td><?php echo $date[0][23] ?></td>
            <td><?php echo $addID[0][23] ?></td>
            <td><?php echo $image_URL[0][23] ?></td>
            <td><?php echo $location[0][23] ?></td>
            <td><?php echo $list_items[0][23] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][23] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][24] ?></td>
            <td><?php echo $desc[0][24] ?></td>
            <td><?php echo $date[0][24] ?></td>
            <td><?php echo $addID[0][24] ?></td>
            <td><?php echo $image_URL[0][24] ?></td>
            <td><?php echo $location[0][24] ?></td>
            <td><?php echo $list_items[0][24] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][24] ?></td>

        </tr>
        <tr>
            <td><?php echo $title[0][25] ?></td>
            <td><?php echo $desc[0][25] ?></td>
            <td><?php echo $date[0][25] ?></td>
            <td><?php echo $addID[0][25] ?></td>
            <td><?php echo $image_URL[0][25] ?></td>
            <td><?php echo $location[0][25] ?></td>
            <td><?php echo $list_items[0][25] ?></td>
            <td class='long-des'><?php echo $long_desctiption[0][25] ?></td>

        </tr>
        <?php


        ?>
    </table>
<?php
}
?>