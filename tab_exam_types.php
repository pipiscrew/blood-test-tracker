<?php
@session_start();

if (!isset($_SESSION["id"])) {
	header("Location: login.php");
	exit ;
}


$exam_groups_rows=null;
///////////////////READ exam_groups
	$find_sql = "SELECT * FROM `exam_groups` order by examgrp_name";
	$stmt      = $db->getConnection()->prepare($find_sql);
	
	$stmt->execute();
	$exam_groups_rows = $stmt->fetchAll();
///////////////////READ exam_groups


?>

		<script>

			$(function ()
				{

					///////////////////////////////////////////////////////////// FILL exam_groups
					var jArray_exam_groups =   <?php echo json_encode($exam_groups_rows); ?>;

					var combo_exam_groups_rows = "<option value='0'></option>";
					for (var i = 0; i < jArray_exam_groups.length; i++)
					{
						combo_exam_groups_rows += "<option value='" + jArray_exam_groups[i]["examgrp_id"] + "'>" + jArray_exam_groups[i]["examgrp_name"] + "</option>";
					}

					$("[name=exam_group_id]").html(combo_exam_groups_rows);
					$("[name=exam_group_id]").change(); //select row 0 - no conflict on POST validation @ PHP
					///////////////////////////////////////////////////////////// FILL exam_groups

					//http://wenzhixin.net.cn/p/bootstrap-table/docs/examples.html#via-javascript-table
					$('#exam_types_tbl').bootstrapTable();

					//new record
					$('#btn_exam_types_new').on('click', function(e)
					{
						$('#lblTitle_EXAM_TYPES').html("New Type");
						
						$('#modalEXAM_TYPES').modal('toggle');
					});
						
					//edit record
					$('#btn_exam_types_edit').on('click', function(e)
					{
						var row = $('#exam_types_tbl').bootstrapTable('getSelections');

						if (row.length>0)
							{
								query_EXAM_TYPES_modal(row[0].exam_type_id);
							}
						else 
							alert("Please select a row");
					});
					
					//delete record
					$('#btn_exam_types_delete').on('click', function(e)
					{
						var row = $('#exam_types_tbl').bootstrapTable('getSelections');

						if (row.length>0)
							{
								if (confirm("Would you like to delete " + row[0].exam_acronym + " ?"))
									delete_EXAM_TYPES(row[0].exam_type_id);
							}
						else 
							alert("Please select a row");
					});
					

				    ////////////////////////////////////////
				    // MODAL FUNCTIONALITIES [START]
				    //when modal closed, hide the warning messages + reset
				    $('#modalEXAM_TYPES').on('hidden.bs.modal', function() {
				        //when close - clear elements
				        $('#formEXAM_TYPES').trigger("reset");
				 
				    });
				 
				    //functionality when the modal already shown and its long, when reloaded scroll to top
				    $('#modalEXAM_TYPES').on('shown.bs.modal', function() {
				        $(this).animate({
				            scrollTop : 0
				        }, 'slow');
				    });
				    // MODAL FUNCTIONALITIES [END]
				    ////////////////////////////////////////
				    
					function isNumeric(obj){
						return !Array.isArray( obj ) && (obj - parseFloat( obj ) + 1) >= 0;
					}

					////////////////////////////////////////
					// MODAL SUBMIT aka save & update button
					$('#formEXAM_TYPES').submit(function(e) {
					    e.preventDefault();
					 
					    ////////////////////////// validation
					    var form = $(this);
						
						if (!isNumeric($('[name=exam_chart_visible]').val()))
						{
							alert("please enter 0 or 1 in 'Chart is visible'");
							return;
						}

						if (!isNumeric($('[name=exam_group_sort_order]').val()))
						{
							alert("please enter a number at 'Sort order'");
							return;
						}
					    ////////////////////////// validation
					 
					    var postData = $(this).serializeArray();
					    var formURL = $(this).attr("action");
					 
					 	loading.appendTo($('#formEXAM_TYPES'));

					    //close modal
					    //$('#modalEXAM_TYPES').modal('toggle');
					 
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
									$('#exam_types_tbl').bootstrapTable('refresh');

								    //close modal
					    			$('#modalEXAM_TYPES').modal('toggle');
								}
					            else
					                alert("ERROR");
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
				function queryParamsEXAM_TYPES(params)
				{
					var q = {
						"limit": params.limit,
						"offset": params.offset,
						"search": params.search,
						"name": params.sort,
						"order": params.order
					};
 
					return q;
				}
				


				//edit button - read record
				function query_EXAM_TYPES_modal(rec_id){
					loading.appendTo(document.body);
					
				    $.ajax(
				    {
				        url : "tab_exam_types_fetch.php",
				        type: "POST",
				        data : { exam_type_id : rec_id },
				        success:function(data, textStatus, jqXHR)
				        {
							loading.remove();
							
				        	if (data!='null')
							{
							 	$("[name=exam_typesFORM_updateID]").val(data.exam_type_id);
								$('[name=exam_group_id]').val(data.exam_group_id);
								$('[name=exam_acronym]').val(data.exam_acronym);
								$('[name=exam_name_eng]').val(data.exam_name_eng);
								$('[name=exam_name_gr]').val(data.exam_name_gr);
								$('[name=exam_mes_type]').val(data.exam_mes_type);
								$('[name=exam_chart_visible]').val(data.exam_chart_visible);
								$('[name=exam_suggested_values]').val(data.exam_suggested_values);
								$('[name=exam_values_explain]').val(data.exam_values_explain);
								$('[name=exam_description]').val(data.exam_description);
								$('[name=exam_group_sort_order]').val(data.exam_group_sort_order);

							 	
							 	$('#lblTitle_EXAM_TYPES').html("Edit Type");
								$('#modalEXAM_TYPES').modal('toggle');
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
				function delete_EXAM_TYPES(rec_id){
					loading.appendTo(document.body);
					
				    $.ajax(
				    {
				        url : "tab_exam_types_delete.php",
				        type: "POST",
				        data : { exam_type_id : rec_id },
				        success:function(data, textStatus, jqXHR)
				        {
				        	loading.remove();
				        	
				        	if (data=='00000')
							{
								//refresh
								$('#exam_types_tbl').bootstrapTable('refresh');
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
				
				
					
		</script>

		<div class="container">
		<br>

			<button id="btn_exam_types_new" type="button" class="btn btn-success">
				New
			</button>
			<button id="btn_exam_types_edit" type="button" class="btn btn-primary">
				Edit
			</button>
			<button id="btn_exam_types_delete" type="button" class="btn btn-danger">
				Delete
			</button> 
			<br><br>
			<table id="exam_types_tbl"
	           data-toggle="table"
	           data-striped=true
	           data-url="tab_exam_types_pagination.php"
			   data-show-columns="false"
			   data-search="false"
			   data-show-refresh="false"
			   data-show-toggle="false"
	           data-pagination="true"
	           data-click-to-select="true" data-single-select="true"
	           data-page-size="10"
	           data-height="480"
	           data-side-pagination="server"
			   data-sort-name="exam_group_id"
			   data-sort-order="asc"
	           data-query-params="queryParamsEXAM_TYPES">

				<thead>
					<tr>
						<th data-field="state" data-checkbox="true" >
						</th>

						<th data-field="exam_type_id" data-visible="false">
							exam_type_id
						</th>
						
						<th data-field="exam_group_id" data-sortable="true">
						Group
						</th>
						
						<th data-field="exam_acronym" data-sortable="true">
						Acronym
						</th>
						
						<th data-field="exam_name_eng" data-sortable="true">
						Name ENG
						</th>
						
						<th data-field="exam_name_gr" data-sortable="true">
						Name GR
						</th>

						<th data-field="exam_mes_type" data-sortable="true">
						Measurement
						</th>
						
						<th data-field="exam_chart_visible" data-sortable="true">
						Visible in chart
						</th>
						
						<th data-field="exam_suggested_values" data-sortable="true">
						Suggested values
						</th>
						
						<th data-field="exam_group_sort_order" data-sortable="true">
						Sort order
						</th>
						
					</tr>
				</thead>
			</table	>
		</div>



<!-- NEW EXAM_TYPES MODAL [START] -->
<div class="modal fade" id="modalEXAM_TYPES" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_EXAM_TYPES'>New</h4>
			</div>
			<div class="modal-body">
				<form id="formEXAM_TYPES" role="form" method="post" action="tab_exam_types_save.php">

			
				<div class='form-group'>
					<label>Group :</label>
					<select id="exam_group_id" name='exam_group_id' class='form-control'>
					</select>
				</div>

				<div class='form-group'>
					<label>Acronym :</label>
					<input name='exam_acronym' class='form-control' maxlength="9999" placeholder='acronym' required>
				</div>

			
				<div class='form-group'>
					<label>Name ENG :</label>
					<input name='exam_name_eng' class='form-control' maxlength="9999" placeholder='name in english' required>
				</div>


			
				<div class='form-group'>
					<label>Name GR :</label>
					<input name='exam_name_gr' class='form-control' maxlength="9999" placeholder='name in greek' required>
				</div>

				<div class='form-group'>
					<label>Measurement type :</label>
					<input name='exam_mes_type' class='form-control' maxlength="9999" placeholder='measurement type ex. mg/dL' required>
				</div>
			
				<div class='form-group'>
					<label>Visible in chart (0= not 1= visible) :</label>
					<input name='exam_chart_visible' class='form-control' maxlength="9999" placeholder='a number' required>
				</div>

			
				<div class='form-group'>
					<label>Suggested values :</label>
					<textarea name='exam_suggested_values' class='form-control' maxlength="9999" placeholder='suggested values' style="resize: none;" rows="3"></textarea>
				</div>


			
				<div class='form-group'>
					<label>Values explaination :</label>
					<textarea name='exam_values_explain' class='form-control' maxlength="9999" placeholder='explaination' style="resize: none;" rows="3"></textarea>
				</div>


			
				<div class='form-group'>
					<label>Description :</label>
					<textarea name='exam_description' class='form-control' maxlength="9999" placeholder='description' style="resize: none;" rows="3"></textarea>
				</div>

			
				<div class='form-group'>
					<label>Sort order :</label>
					<input name='exam_group_sort_order' class='form-control' maxlength="9999" placeholder='a number'>
				</div>



						<!-- <input name="exam_typesFORM_FKid" id="EXAM_TYPES_FKid" class="form-control" style="display:none;"> -->
						<input name="exam_typesFORM_updateID" id="exam_typesFORM_updateID" class="form-control" style="display:none;">

						<div class="modal-footer">
							<button id="bntCancel_EXAM_TYPES" type="button" class="btn btn-default" data-dismiss="modal">
								cancel
							</button>
							<button id="bntSave_EXAM_TYPES" class="btn btn-primary" type="submit" name="submit">
								save
							</button>
						</div>
                </form>
            </div><!-- End of Modal body -->
        </div><!-- End of Modal content -->
    </div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<!-- NEW EXAM_TYPES MODAL [END] -->
