import React from 'react';
import { StyleSheet, ViewStyle, TextStyle, ImageStyle } from 'react-native';
import { ApplicationNavigationProp } from '../../navigation/ApplicationNavigator';
import { CBackgroundGradient } from '../../components/landing/CBackgroundGradient';
import { CHeader } from '../../components/header/CHeader';
import { CButton } from '../../components/button/CButton';
import { CScrollView } from '../../components/view/CScrollView';
import { CFlexView } from '../../components/view/CFlexView';
import { CText } from '../../components/text/CText';
import { useTranslation, FALLBACK_IMAGES, StorageService, CONST_OUTPUT_TYPE_IMAGE, STORAGE_CATEGORIES } from 'app-shared';
import { CONST_ICON_CHECK, CONST_ICON_REMOVE, CONST_COLORS } from '../../style/index';
import { connect } from 'react-redux';
import { getActiveTeam, IState, AppState, getActiveSessionInstance } from '../../redux/State';
import { removeTeam, removePlayer, startGame, setAppState } from '../../redux/Actions';
import { ITeam, IPlayer, IAuthState } from 'app-shared';
import { CImage } from '../../components/image/CImage';


interface IProps {
    setAppState: (state:AppState) => void;
    navigation: ApplicationNavigationProp;
    team: ITeam|null;
    auth: IAuthState;
    removeTeam:(pTeamKey:string) => void;
    removePlayer:(pTeamKey:string, pPlayerID:string) => void;
    startGame:() => void;
}

export const TeamLeaderOverviewInner:React.FunctionComponent<IProps> = (props:IProps) => {
    const { t } = useTranslation();

    if(props.team === null || props.auth.uid === null) {
        return null;
    }

    const onBackPress = () => {
        props.setAppState(AppState.TEAM_OVERVIEW);
    };

    const onRemoveTeamPress = () => {
        props.removeTeam(props.team!.key!);
    };

    const onRemovePlayerPress = (pPlayer:IPlayer) => {
        props.removePlayer(props.team!.key!, pPlayer.key);
    };

    const onStartGamePress = () => {
        props.startGame();
    };

    if(props.team === null) {
        return null;
    }

    return (
        <CFlexView flagIsScreenContainer={true}>
            <CBackgroundGradient />
            <CScrollView>
                <CHeader
                    title={props.team.team_name}
                    titleBold={true}
                    showBackButton={true}
                    onBackPress={onBackPress}
                    showSettingsButton={true}
                    showLogo={true}
                    navigation={props.navigation}
                />
                <CFlexView>
                    <CImage 
                        source={StorageService.getPathWithFireStoreFolders(STORAGE_CATEGORIES.TeamProfile, CONST_OUTPUT_TYPE_IMAGE) + '/' + StorageService.getFileNameForUserAndTeamProfileFiles(props.team.key, CONST_OUTPUT_TYPE_IMAGE)} 
                        fallback={FALLBACK_IMAGES.AVATAR_MALE}
                        containerStyle={styles.avatarContainerStyle}
                        imageStyle={styles.avatarStyle}
                        resizeMode="cover"
                    />
                </CFlexView>
                {
                    props.team.players.map((player) => {
                        let rightIcon = {};
                        if(player.key !== props.auth.uid) {
                            rightIcon = {
                                rightIcon: CONST_ICON_REMOVE,
                                rightIconColor: CONST_COLORS.SEPTENARY.color,
                                onPressRightIcon: ()=> { onRemovePlayerPress(player); }
                            };
                        }
                        return (
                            <CFlexView viewStyle={styles.teamMemberView} key={player.key}>
                                <CButton
                                    containerStyle={styles.teamMemberButtonContainer}
                                    text={player.player_name}
                                    {...rightIcon}
                                />
                            </CFlexView>
                        );
                    })
                }
                <CFlexView viewStyle={styles.removeTeamView}>
                    <CButton
                        containerStyle={styles.removeTeamButtonContainer}
                        text={t('TEAM_LEADER_OVERVIEW_SCREEN.LEAVE_AND_REMOVE_TEAM')}
                        textStyle={styles.removeTeamText}
                        leftIcon={CONST_ICON_REMOVE}
                        onPress={onRemoveTeamPress}

                    />
                </CFlexView>
                <CFlexView viewStyle={styles.textStartInfoView}>
                    <CText
                        text={t('TEAM_LEADER_OVERVIEW_SCREEN.INFO_ABOUT_START_GAME')}
                    />
                </CFlexView>
                <CFlexView>
                    <CButton
                        text={t('GENERAL.START_GAME')}
                        leftIcon={CONST_ICON_CHECK}
                        leftIconColor={CONST_COLORS.TERTIARY.color}
                        onPress={onStartGamePress}
                    />
                </CFlexView>
            </CScrollView>
        </CFlexView>
    );
};

export const TeamLeaderOverviewScreen = connect(
    (pState:IState) => {
        const session_instance = getActiveSessionInstance(pState);
        return {
            team: session_instance?getActiveTeam(pState)!:null,
            auth: pState.auth
        };
    }, {
        removeTeam,
        removePlayer,
        startGame,
        setAppState
    }
)(TeamLeaderOverviewInner);

interface IStyles {
    teamMemberView: ViewStyle;
    teamMemberButtonContainer: ViewStyle;
    removeTeamView: ViewStyle;
    removeTeamButtonContainer: ViewStyle;
    removeTeamText: TextStyle;
    readyButton: ViewStyle;
    textStartInfoView: ViewStyle;
    avatarStyle: ImageStyle;
    avatarContainerStyle: ViewStyle;
}

const styles = StyleSheet.create<IStyles>({
    teamMemberView: {
        marginBottom: 15
    },
    teamMemberButtonContainer: {
        backgroundColor: 'transparent',
        borderWidth: 0.5,
        borderColor: '#FFF',
        borderRadius: 20
    },
    removeTeamView: {
        marginTop: 10,
        marginBottom: 10
    },
    removeTeamButtonContainer: {
        backgroundColor: CONST_COLORS.SECONDARY.color
    },
    removeTeamText: {
        color: CONST_COLORS.QUATERNARY.color
    },
    readyButton: {
        marginBottom: 20
    },
    textStartInfoView: {
        marginBottom: 10
    },
    avatarStyle: {
        width:100,
        height:100,
        borderRadius: 50,
        overflow: "hidden"
    },
    avatarContainerStyle: {
        height: 100,
        alignContent: 'center',
        alignItems: 'center',
        marginTop: 10,
        marginBottom: 25
    }
});
