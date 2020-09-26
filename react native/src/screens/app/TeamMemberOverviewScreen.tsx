import React from 'react';
import { StyleSheet, ViewStyle, TextStyle } from 'react-native';
import { ApplicationNavigationProp } from '../../navigation/ApplicationNavigator';
import { CBackgroundGradient } from '../../components/landing/CBackgroundGradient';
import { CHeader } from '../../components/header/CHeader';
import { CButton } from '../../components/button/CButton';
import { CScrollView } from '../../components/view/CScrollView';
import { CFlexView } from '../../components/view/CFlexView';
import { CText } from '../../components/text/CText';
import { CInput } from '../../components/input/CInput';
import { useTranslation } from 'app-shared';
import { CONST_ICON_CHECK, CONST_COLORS, CONST_ICON_INPUT_EDIT, CONST_ICON_CROSS_CIRCLE, FONTSIZE, CONST_ICON_CROSS } from '../../style/index';
import { ITeam, IAuthState } from 'app-shared';
import { IState, getActiveTeam, AppState } from '../../redux/State';
import { addPlayer, removePlayer, setAppState } from '../../redux/Actions';
import { connect } from 'react-redux';
import { useState } from 'react';
import { stringNotEmpty } from '../../utilities/Validation';


interface IProps {
    navigation: ApplicationNavigationProp;
    team: ITeam;
    auth: IAuthState;
    setAppState:(state:AppState)=>void;
    addPlayer:(pTeamKey:string, pPlayerName:string)=>void;
    removePlayer:(pTeamKey:string, pPlayerID:string)=>void;
}

export const TeamMemberOverviewInner:React.FunctionComponent<IProps> = (props:IProps) => {
    const { t } = useTranslation();
    const [player_name, setPlayerName] = useState("");
    const [player_highlight, setPlayerHighlight] = useState(false);

    if(props.team === null || props.auth.uid === null) {
        return null;
    }

    const player = props.team.players.find((pPlayer) => pPlayer.key === props.auth.uid);

    /* const onBackPress = () => {
        props.setAppState(AppState.TEAM_OVERVIEW);
    }; */

    const onChangePlayerName = (player_name:string) => {
        setPlayerName(player_name);
        setPlayerHighlight(false);
    };

    const onJoinTeamPress = () => {
        const required_player = stringNotEmpty(player_name);
        if(required_player) {
            props.addPlayer(props.team.key!, player_name);
        } else {
            setPlayerHighlight(!required_player);
        }
    };

    const onLeaveTeamPress = () => {
        props.removePlayer(props.team.key!, props.auth.uid!);
    };

    if(props.team === null) {
        return null;
    }

    return (
        <CFlexView flagIsScreenContainer={true}>
            <CBackgroundGradient />
            <CScrollView>
                <CHeader
                    title={t('TEAM_MEMBER_OVERVIEW_SCREEN.TITLE')}
                    titleBold={true}
                    showBackButton={true}
                    
                    showSettingsButton={true}
                    navigation={props.navigation}
                />
                <CFlexView viewStyle={styles.inputNameView}>
                    <CInput
                        placeholder={t('GENERAL.NAME_C')}
                        rightIcon={CONST_ICON_INPUT_EDIT}
                        onChangeText={onChangePlayerName}
                        value={player_name}
                        highlight={player_highlight}
                    />
                </CFlexView>
                <CFlexView viewStyle={styles.teamView}>
                    <CText
                        text={props.team.team_name}
                        textStyle={styles.teamText}
                    />
                </CFlexView>
                {
                    props.team.players.map((player) => {
                        return (
                            <CFlexView viewStyle={styles.teamMemberView} key={player.key}>
                                <CButton
                                    containerStyle={styles.teamMemberButtonContainer}
                                    textStyle={styles.teamMemberButtonText}
                                    key={player.key}
                                    text={player.player_name}
                                />
                            </CFlexView>
                        );
                    })
                }
                {!player && 
                    <CFlexView viewStyle={styles.joinTeamView}>
                        <CButton
                            text={t('TEAM_MEMBER_OVERVIEW_SCREEN.JOIN_THIS_TEAM')}
                            leftIcon={CONST_ICON_CHECK}
                            leftIconColor={CONST_COLORS.TERTIARY.color}
                            onPress={onJoinTeamPress}
                        />
                    </CFlexView>
                }
                {player &&
                    <CFlexView viewStyle={styles.leaveTeamView}>
                        <CButton
                            containerStyle={styles.leaveTeamButtonContainer}
                            text={t('TEAM_MEMBER_OVERVIEW_SCREEN.LEAVE_TEAM')}
                            textStyle={styles.leaveTeamText}
                            leftIcon={CONST_ICON_CROSS_CIRCLE}
                            onPress={onLeaveTeamPress}
                        />
                    </CFlexView>
                }
                <CFlexView>
                    <CText
                        text={t('TEAM_MEMBER_OVERVIEW_SCREEN.INFO_ABOUT_WHEN_GAME_STARTS')}
                        textStyle={styles.waitText}
                    />
                </CFlexView>
            </CScrollView>
        </CFlexView>
    );
};

export const TeamMemberOverviewScreen = connect(
    (pState:IState) => ({
        team: getActiveTeam(pState)!,
        auth: pState.auth
    }), {
        addPlayer,
        removePlayer,
        setAppState
    }
)(TeamMemberOverviewInner);

interface IStyles {
    inputNameView: ViewStyle;
    teamView: ViewStyle;
    readyButton: ViewStyle;
    teamText: TextStyle;
    teamMemberView: ViewStyle;
    teamMemberButtonContainer: ViewStyle;
    teamMemberButtonText: TextStyle;
    joinTeamView: ViewStyle;
    leaveTeamView: ViewStyle;
    leaveTeamButtonContainer: ViewStyle;
    leaveTeamText: TextStyle;
    waitText: TextStyle;
}

const styles = StyleSheet.create<IStyles>({
    inputNameView: {
        marginBottom: 20
    },
    teamView: {
        marginBottom: 20
    },
    readyButton: {
        marginBottom: 20
    },
    teamText:{
        fontSize: FONTSIZE.XLARGE
    },
    teamMemberView: {
        marginBottom: 15
    },
    teamMemberButtonContainer: {
        backgroundColor: 'transparent',
        borderWidth: 0.5,
        borderColor: '#FFF',
        borderRadius: 20
    },
    teamMemberButtonText: {
        color: CONST_COLORS.QUATERNARY.color
    },
    joinTeamView: {
        marginBottom: 20
    },
    leaveTeamView: {
        marginBottom: 20
    },
    leaveTeamButtonContainer: {
        backgroundColor: CONST_COLORS.SECONDARY.color
    },
    leaveTeamText: {
        color: CONST_COLORS.QUATERNARY.color
    },
    waitText: {
        fontSize: FONTSIZE.LARGE
    }
});
