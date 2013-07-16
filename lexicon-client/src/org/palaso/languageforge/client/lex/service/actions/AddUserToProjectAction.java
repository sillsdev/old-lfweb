package org.palaso.languageforge.client.lex.service.actions;

import org.palaso.languageforge.client.lex.common.BaseConfiguration;
import org.palaso.languageforge.client.lex.jsonrpc.JsonRpcAction;
import org.palaso.languageforge.client.lex.model.UserListDto;

public class AddUserToProjectAction extends JsonRpcAction<UserListDto> {

	private String userId;
	private String projectId;

	public AddUserToProjectAction(String userId, String projectId) {
		super(BaseConfiguration.getInstance().getLFApiPath(), BaseConfiguration.getInstance().getApiFileName(), "addUserToProjectForLex", 2);
		this.userId = userId;
		this.projectId = projectId;
	}

	public String encodeParam(int i) {
		switch (i) {
		case 0:
			return projectId;
		case 1:
			return userId;
		}
		return null;
	}
}