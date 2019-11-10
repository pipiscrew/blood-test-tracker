<?php
@session_start();

if (!isset($_SESSION["id"])) {
	header("Location: login.php");
	exit ;
}



// $blood_tests_rows=null;
// ///////////////////READ blood_tests
// 	$find_sql = "SELECT * FROM `blood_tests` order by blood_date";
// 	$stmt      = $db->prepare($find_sql);
	
// 	$stmt->execute();
// 	$blood_tests_rows = $stmt->fetchAll();
// ///////////////////READ blood_tests


$exam_types_rows=null;
///////////////////READ exam_types
	$find_sql = "SELECT * FROM `exam_types` order by exam_group_id";
	$stmt      = $db->getConnection()->prepare($find_sql);
	
	$stmt->execute();
	$exam_types_rows = $stmt->fetchAll();
///////////////////READ exam_types


?>

		<script>
			$(function ()
				{

	///////////////////////////////////////////////////////////// FILL blood_tests
	// var jArray_blood_tests =   <?php //echo json_encode($blood_tests_rows); ?>;

	// var combo_blood_tests_rows = "<option value='0'></option>";
	// for (var i = 0; i < jArray_blood_tests.length; i++)
	// {
	// 	combo_blood_tests_rows += "<option value='" + jArray_blood_tests[i]["blood_id"] + "'>" + jArray_blood_tests[i]["blood_date"] + "</option>";
	// }

	// $("[name=blood_test_id]").html(combo_blood_tests_rows);
	// $("[name=blood_test_id]").change(); //select row 0 - no conflict on POST validation @ PHP
	///////////////////////////////////////////////////////////// FILL blood_tests


	///////////////////////////////////////////////////////////// FILL exam_types
	var jArray_exam_types =   <?php echo json_encode($exam_types_rows); ?>;

	var combo_exam_types_rows = "<option value='0'></option>";
	for (var i = 0; i < jArray_exam_types.length; i++)
	{
		combo_exam_types_rows += "<option value='" + jArray_exam_types[i]["exam_type_id"] + "'>" + jArray_exam_types[i]["exam_acronym"]+' ('+jArray_exam_types[i]["exam_name_eng"]+') ' + jArray_exam_types[i]["exam_mes_type"] + "</option>";
	}

	$("[name=exam_type_id]").html(combo_exam_types_rows);
	$("[name=exam_type_id]").change(); //select row 0 - no conflict on POST validation @ PHP
	///////////////////////////////////////////////////////////// FILL exam_types




					//http://wenzhixin.net.cn/p/bootstrap-table/docs/examples.html#via-javascript-table
					$('#blood_test_exams_tbl').bootstrapTable();

					//new record
					$('#btn_blood_test_exams_new').on('click', function(e)
					{
						//check the parent table
						var row = $('#blood_tests_tbl').bootstrapTable('getSelections');

						if (row.length>0)
							{
								$("[name=blood_test_id]").val(row[0].blood_id);
							}
						else 
							{alert("Please select a parent row (blood test)");return;}
						//check the parent table

						$('#lblTitle_BLOOD_TEST_EXAMS').html("New exam type for " + row[0].blood_date + " (" + row[0].blood_where +")");
						
						$('#modalBLOOD_TEST_EXAMS').modal('toggle');
					});
						
					//edit record
					$('#btn_blood_test_exams_edit').on('click', function(e)
					{
						var row = $('#blood_test_exams_tbl').bootstrapTable('getSelections');

						if (row.length>0)
							{
								query_BLOOD_TEST_EXAMS_modal(row[0].blood_test_exam_id);
							}
						else 
							alert("Please select a row");
					});
					
					//delete record
					$('#btn_blood_test_exams_delete').on('click', function(e)
					{
						var row = $('#blood_test_exams_tbl').bootstrapTable('getSelections');

						if (row.length>0)
							{
								if (confirm("Would you like to delete " + row[0].comments + " ?"))
									delete_BLOOD_TEST_EXAMS(row[0].blood_test_exam_id);
							}
						else 
							alert("Please select a row");
					});
					

				    ////////////////////////////////////////
				    // MODAL FUNCTIONALITIES [START]
				    //when modal closed, hide the warning messages + reset
				    $('#modalBLOOD_TEST_EXAMS').on('hidden.bs.modal', function() {
				        //when close - clear elements
				        $('#formBLOOD_TEST_EXAMS').trigger("reset");
				    });
				 
				    //functionality when the modal already shown and its long, when reloaded scroll to top
				    $('#modalBLOOD_TEST_EXAMS').on('shown.bs.modal', function() {
				        $(this).animate({
				            scrollTop : 0
				        }, 'slow');
				    });
				    // MODAL FUNCTIONALITIES [END]
				    ////////////////////////////////////////
				    

					function validatedecimal(s) {
						var rgx = /^[0-9]*\.?[0-9]*$/;
						return s.match(rgx);
					}

					////////////////////////////////////////
					// MODAL SUBMIT aka save & update button
					$('#formBLOOD_TEST_EXAMS').submit(function(e) {
					    e.preventDefault();
					 
					    ////////////////////////// validation
					    var form = $(this);
						
						if (!validatedecimal($('[name=exam_value]').val())){
							alert("the value must be decimal ex. 1.5");
							return;
						}

					    ////////////////////////// validation
					 
					    var postData = $(this).serializeArray();
					    var formURL = $(this).attr("action");
					 
					 	loading.appendTo($('#formBLOOD_TEST_EXAMS'));

					    //close modal
					    //$('#modalBLOOD_TEST_EXAMS').modal('toggle');
					 
					    $.ajax(
					    {
					        url : formURL,
					        type: "POST",
					        data : postData,
					        success:function(data, textStatus, jqXHR)
					        {
								loading.remove();

					            if (data=="00000")
								{	//refresh
									$('#blood_test_exams_tbl').bootstrapTable('refresh');

								    //close modal
					    			$('#modalBLOOD_TEST_EXAMS').modal('toggle');
								}
					            else
					                alert(data);
					        },
					        error: function(jqXHR, textStatus, errorThrown)
					        {
					        	loading.remove();
					            alert("ERROR - connection error");
					        }
					    });
					});

				}); //jQ ends
				
				//bootstrap-table
				function queryParamsBLOOD_TEST_EXAMS(params)
				{
					var q = {
						"limit": params.limit,
						"offset": params.offset,
						"search": params.search,
						"name": params.sort,
						"order": params.order,
						"parent_id" : blood_test_id_only_for_fill_grid
					};
 
					return q;
				}
				
				//edit button - read record
				function query_BLOOD_TEST_EXAMS_modal(rec_id){
					loading.appendTo(document.body);
					
				    $.ajax(
				    {
				        url : "tab_blood_test_exams_fetch.php",
				        type: "POST",
				        data : { blood_test_exam_id : rec_id },
				        success:function(data, textStatus, jqXHR)
				        {
							loading.remove();
							
				        	if (data!='null')
							{
							 	$("[name=blood_test_examsFORM_updateID]").val(data.blood_test_exam_id);
								$('[name=blood_test_id]').val(data.blood_test_id);
								$('[name=exam_type_id]').val(data.exam_type_id);
								$('[name=exam_value]').val(data.exam_value);
								$('[name=comments]').val(data.comments);

							 	
							 	$('#lblTitle_BLOOD_TEST_EXAMS').html("Edit BLOOD_TEST_EXAMS");
								$('#modalBLOOD_TEST_EXAMS').modal('toggle');
							}
							else
								alert("ERROR - Cant read the record.");
				        },
				        error: function(jqXHR, textStatus, errorThrown)
				        {
				        	loading.remove();
				            alert("ERROR");
				        }
				    });
				}
				
				//delete button - delete record
				function delete_BLOOD_TEST_EXAMS(rec_id){
					loading.appendTo(document.body);
					
				    $.ajax(
				    {
				        url : "tab_blood_test_exams_delete.php",
				        type: "POST",
				        data : { blood_test_exam_id : rec_id },
				        success:function(data, textStatus, jqXHR)
				        {
				        	loading.remove();
				        	
				        	if (data=='00000')
							{
								//refresh
								$('#blood_test_exams_tbl').bootstrapTable('refresh');
							}
							else
								alert("ERROR - Cant delete the record.");
				        },
				        error: function(jqXHR, textStatus, errorThrown)
				        {
				        	loading.remove();
				            alert("ERROR");
				        }
				    });
				}
				
				
				function redirect_insert_mass(){

					var row = $('#blood_tests_tbl').bootstrapTable('getSelections');

						if (row.length>0)
							{
								var win = window.open('tab_blood_test_exams_mass.php?blood_test_id=' + row[0].blood_id, '_blank');
  								win.focus();
							}
						else 
							{
								alert("Please select a bloo test on the left");
								return;
							}


				}	
		</script>

	</head>
	<body>
		<!-- <div class="container"> -->
			<br>
			<button onclick="redirect_insert_mass()" type="button" class="btn btn-success">
				New Mass
			</button>
			<button id="btn_blood_test_exams_new" type="button" class="btn btn-success">
				New
			</button>
			<button id="btn_blood_test_exams_edit" type="button" class="btn btn-primary">
				Edit
			</button>
			<button id="btn_blood_test_exams_delete" type="button" class="btn btn-danger">
				Delete
			</button> 
			<span id="selected_blood_test" class="label label-danger" style="float:right">New</span>
			<br><br>
			<table id="blood_test_exams_tbl"
	           data-toggle="table"
	           data-striped=true
	           data-url="tab_blood_test_exams_pagination.php"
			   data-show-columns="false"
			   data-search="false"
			   data-show-refresh="false"
			   data-show-toggle="false"
	           data-pagination="true"
	           data-click-to-select="true" data-single-select="true"
	           data-page-size="10"
	           data-height="480"
	           data-side-pagination="server"
			   data-sort-name="exam_type_id"
			   data-sort-order="asc"
	           data-query-params="queryParamsBLOOD_TEST_EXAMS">

				<thead>
					<tr>
						<th data-field="state" data-checkbox="true" >
						</th>

						<th data-field="blood_test_exam_id" data-visible="false">
							blood_test_exam_id
						</th>
						
						<!-- <th data-field="blood_test_id" data-sortable="true">
							Blood test
						</th> -->
						
						<th data-field="exam_type_id" data-sortable="true">
							Examination Type
						</th>

						<th data-field="exam_value" data-sortable="true">
							Examination Value
						</th>
						
						<th data-field="comments" data-sortable="true">
							Comments
						</th>
						
					</tr>
				</thead>
			</table	>
		<!-- </div> -->



<!-- NEW BLOOD_TEST_EXAMS MODAL [START] -->
<div class="modal fade" id="modalBLOOD_TEST_EXAMS" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_BLOOD_TEST_EXAMS'>New</h4>
			</div>
			<div class="modal-body">
				<form id="formBLOOD_TEST_EXAMS" role="form" method="post" action="tab_blood_test_exams_save.php">

<!-- 			
				<div class='form-group'>
					<label>blood_test_id :</label>
					<select id="blood_test_id" name='blood_test_id' class='form-control'>
					</select>
				</div> -->


			
				<div class='form-group'>
					<label>Type :</label>
					<select id="exam_type_id" name='exam_type_id' class='form-control'>
					</select>
				</div>

				<div class='form-group'>
					<label>Value :</label>
					<input name='exam_value' class='form-control' maxlength="9999" placeholder='examination value' required>
				</div>
			
				<div class='form-group'>
					<label>Comments :</label>
					<textarea name='comments' class='form-control' maxlength="9999" placeholder='comments' style="resize: none;" rows="3"></textarea>
				</div>



						<!-- <input name="blood_test_examsFORM_FKid" id="BLOOD_TEST_EXAMS_FKid" class="form-control" style="display:none;"> -->
						<input name="blood_test_examsFORM_updateID" id="blood_test_examsFORM_updateID" class="form-control" style="display:none;">
						<input name="blood_test_id" id="blood_test_id" class="form-control" style="display:none;">

						<div class="modal-footer">
							<button id="bntCancel_BLOOD_TEST_EXAMS" type="button" class="btn btn-default" data-dismiss="modal">
								cancel
							</button>
							<button id="bntSave_BLOOD_TEST_EXAMS" class="btn btn-primary" type="submit" name="submit">
								save
							</button>
						</div>
                </form>
            </div><!-- End of Modal body -->
        </div><!-- End of Modal content -->
    </div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<!-- NEW BLOOD_TEST_EXAMS MODAL [END] -->
