import * as React from 'react';
import { StyleSheet, ViewStyle, StatusBar, Text, Dimensions, View, ScrollView } from 'react-native';
import { CFlexView } from './CFlexView';
import { CONST_COLORS, CONST_ICON_CROSS, FONTFAMILY, FONTSIZE } from '../../style';
import { CButtonRound } from '../button/CButtonRound';


export interface IProps {
    title:string;
    close:()=>void;
}

export const CModal:React.FunctionComponent<IProps> = (props:React.PropsWithChildren<IProps>) => {
    return (
        <CFlexView flexDirection='column' flex={1} justifyContent={'center'}>
            <View style={[styles.innerContainer]}>
                <CFlexView flexDirection='row' justifyContent={'space-between'} style={[styles.headerContainer]}>
                    { props.title && 
                        <Text style={styles.headerText}>{props.title}</Text>
                    }
                    <CButtonRound
                        icon={CONST_ICON_CROSS}
                        iconColor={CONST_COLORS.QUATERNARY.color}
                        iconContainerStyle={styles.headerContainer}
                        containerStyle={styles.headerIcon}
                        onPress={props.close}>
                    </CButtonRound>
                </CFlexView>
                <ScrollView
                    showsVerticalScrollIndicator={false}
                    bounces={false}
                    style={[styles.contentContainer]}
                >
                    {
                        props.children
                    }
                </ScrollView>
            </View>
        </CFlexView>
    );
};

interface IStyle {
    headerText: ViewStyle;
    headerIcon: ViewStyle;
    headerContainer: ViewStyle;
    innerContainer: ViewStyle;
    contentContainer: ViewStyle;
}

const height = Dimensions.get("window").height;
const top = StatusBar.currentHeight ? StatusBar.currentHeight  + 20 : 60;

const styles = StyleSheet.create<IStyle>({
    headerText: {
        width: "80%",
        fontFamily: FONTFAMILY.REGULAR,
        fontSize: FONTSIZE.LARGE,
        fontWeight: '500',
        color: CONST_COLORS.ZENARY.color
    },
    headerIcon: {
        position: "absolute",
        top: 0,
        right: 0
    },
    headerContainer: {
        paddingTop: 21,
        paddingRight: 21,
        paddingBottom: 21,
        paddingLeft: 21,
        backgroundColor: CONST_COLORS.ZENARY.background
    },
    innerContainer: {
        marginTop: top,
        marginLeft: 20,
        marginRight: 20,
        marginBottom: top*2,
        maxHeight: height - top*3,
        borderRadius: 10,
        backgroundColor: CONST_COLORS.PRIMARY.background,
        overflow: "hidden"
    },
    contentContainer: {
        maxHeight: height - top - 100
    }
});
