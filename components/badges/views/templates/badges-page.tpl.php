<div ng-app="appBadges">
	<div id="decom-badges-page" ng-controller="BadgesCtrl">

		<a class="button button-primary button-large" href="" ng-click="addBadge()" style="margin-bottom: 10px;"><?php _e( 'Add badge', DECOM_LANG_DOMAIN ); ?></a>

		<div>
			<table ng-table="tableParams" class="wp-list-table widefat fixed posts" ng-init="getBadges()">
				<tr ng-repeat="badge in bages" ng-class-odd="'alt'">
					<td data-title="'<?php _e( 'Image', DECOM_LANG_DOMAIN ); ?>'">
						<img width="20" src="{{badge.badge_icon_path}}" />

						<div ng-if="badge.edit">
							<button class="button button-primary button-large" deuploadimg ng-model="badge.badge_icon_path"><?php _e( 'Upload image', DECOM_LANG_DOMAIN ); ?></button>
						</div>
					</td>

					<td data-title="'<?php _e( 'Name of the Badge', DECOM_LANG_DOMAIN ); ?>'">
						<span ng-if="!badge.edit">{{badge.badge_name}}</span>

						<div ng-if="badge.edit">
							<input class="form-control" type="text" ng-model="badge.badge_name" style="width: 100%;" />
						</div>
					</td>

					<td data-title="'<?php _e( 'Assign a badge to achieve', DECOM_LANG_DOMAIN ); ?>'">
						<div ng-if="!badge.edit">
							<span ng-if="badge.assign_badge_achive == 'badge_like_number'"><?php _e( 'total x likes all user comments', DECOM_LANG_DOMAIN ); ?></span>
							<span ng-if="badge.assign_badge_achive == 'badge_dislike_number'"><?php _e( 'total x dislikes all user comments', DECOM_LANG_DOMAIN ); ?></span>
							<span ng-if="badge.assign_badge_achive == 'badge_comments_number'"><?php _e( 'number of comments', DECOM_LANG_DOMAIN ); ?></span>
						</div>
						<div ng-if="badge.edit">

							<select ng-model="badge.assign_badge_achive" style="width:100%">
								<option value="badge_like_number"><?php _e( 'total x likes all user comments', DECOM_LANG_DOMAIN ); ?></option>
								<option ng-selected="badge.badge_dislike_number > 0" value="badge_dislike_number"><?php _e( 'total x dislikes all user comments', DECOM_LANG_DOMAIN ); ?></option>
								<option ng-selected="badge.badge_comments_number > 0" value="badge_comments_number"><?php _e( 'number of comments', DECOM_LANG_DOMAIN ); ?></option>
							</select>
						</div>
					</td>

					<td data-title="'<?php _e( 'Number', DECOM_LANG_DOMAIN ); ?>'">
						<span ng-if="!badge.edit">{{badge.number}}</span>

						<div ng-if="badge.edit">
							<input class="form-control" type="text" ng-model="badge.number" style="width: 100%;" value="{{badge.number}}" />
						</div>
					</td>

					<td>
						<a ng-if="!badge.edit" href="" class="button button-primary button-large" ng-click="editBadge(badge)"><?php _e( 'Edit', DECOM_LANG_DOMAIN ); ?></a>
						<a ng-if="badge.edit" href="" class="button button-primary button-large" ng-click="saveBadge(badge)"><?php _e( 'Save', DECOM_LANG_DOMAIN ); ?></a>
						<a href="" class="button button-primary button-large" ng-click="deleteBadge(badge)"><?php _e( 'Delete', DECOM_LANG_DOMAIN ); ?></a>
					</td>

				</tr>
			</table>
		</div>
	</div>
</div>
