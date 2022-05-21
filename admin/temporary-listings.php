<?php
error_reporting(0);
global $wpdb;
$table_name = $wpdb->prefix . 'ebay_listings';
$select_query =  $wpdb->get_results("SELECT * FROM $table_name ");
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

<style>
    td.tb1-long-des p {
    height: 100px;
    overflow: hidden;
}

</style>

<h3><a href="<?php echo esc_url($check1) ?>">Delete All</a></h3>
<!-- <h3><a href="javascript:void(0)" class='insert_new_post'>Insert In Post</a></h3> -->
    <table class="widefat fixed" cellspacing="0">
        <tr>
            <th>S.no</th>
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

        $entries = -1;
        foreach ($select_query as $sql) {
            $entries++;
            
            $title = json_decode($sql->title);
            $desc[] = json_decode($sql->description);
            $date[] = json_decode($sql->date);
            $addID[] = json_decode($sql->add_id);
            $image_URL[] = json_decode($sql->image_URL);
            $location[] = json_decode($sql->location);
            $list_items[] = json_decode($sql->list_items);
            $long_desctiption[] = json_decode($sql->long_desctiption);
        
        $x = -1;
        $number = 0;
        foreach($title as $tit){
            $number++;
            $x++;
           
            ?>
            <tr class='tb1'>
                <td class='tb1-title1'><?php echo $number; ?></td>
                <td class='tb1-title1'><?php echo $tit; ?></td>
                <td class='tb1-desc1'><?php echo $desc[$entries][$x] ?></td>
                <td class='tb1-date1'><?php echo $date[$entries][$x] ?></td>
                <td class='tb1-addId1'><?php echo $addID[$entries][$x] ?></td>
                <td class='tb1-image_ur1'><?php echo $image_URL[$entries][$x] ?></td>
                <td class='tb1-location1'><?php echo $location[$entries][$x] ?></td>
                <td class='tb1-list_items1'><?php echo $list_items[$entries][$x] ?></td>
                <td class='tb1-long-des'><p><?php echo $long_desctiption[$entries][$x] ?></p></td>
        </tr>
            <?php
        }
    }
        ?>
    </table>
<?php
}
?>