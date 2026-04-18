<div class="table-responsive">
    <table class="table table-striped mb0">
        <tr>
            <th class="border0"><?php echo $this->lang->line('class'); ?></th>
            <td class="border0"><?php echo ($Call_data['class']); ?></td>
            <th class="border0"><?php echo $this->lang->line('section'); ?></th>
            <td class="border0"><?php echo $Call_data['section'] ?></td>
            <th class="border0"><?php echo "Name"; ?></th>
            <td class="border0"><?php echo $Call_data['name']; ?></td>
        </tr>

        <tr>

            <th><?php echo $this->lang->line('staff'); ?></th>
            <td><?php echo $Call_data['collected_by']; ?></td>
        </tr>
        <tr>

            <th><?php echo $this->lang->line('date'); ?></th>
            <td><?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($Call_data['date'])); ?></td>
        </tr>


        <tr>
            <th>Handwriting</th>
            <td colspan="3"><?php echo ($Call_data['parameter_1']); ?></td>
        </tr>


        <tr>
            <th>Listening</th>
            <td colspan="3"><?php echo ($Call_data['parameter_2']); ?></td>
        </tr>
        <tr>
            <th>Behaviour In Class Room</th>
            <td colspan="3"><?php echo ($Call_data['parameter_3']); ?></td>
        </tr>
        <tr>
            <th>Behaviour With Teachers</th>
            <td colspan="3"><?php echo ($Call_data['parameter_4']); ?></td>
        </tr>
        <tr>
            <th>Behaviour With Classmates / Elders And Youngers</th>
            <td colspan="3"><?php echo ($Call_data['parameter_5']); ?></td>
        </tr>
        <tr>
            <th>Behaviour In Campus</th>
            <td colspan="3"><?php echo ($Call_data['parameter_6']); ?></td>
        </tr>
        <tr>
            <th>Bike</th>
            <td colspan="3"><?php echo ($Call_data['parameter_7']); ?></td>
        </tr>

    </table>
</div>