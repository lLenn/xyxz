import React from 'react';
import { ScrollView, View, ViewStyle, StyleSheet, StatusBar, ActivityIndicator } from 'react-native';
import { CONST_COLORS } from '../../style';

interface IScrollView {
    containerStyle?: ViewStyle;
    loading?: boolean;
}

export class CScrollView extends React.PureComponent<IScrollView> {
    render() {
        const {
            containerStyle,
            loading
        } = this.props;
        return (
            <View style={styles.adjustmentContainer}>
                <ScrollView
                    style={styles.scrollView}
                    showsVerticalScrollIndicator={false}
                    bounces={false}
                >
                    <View style={[styles.container, containerStyle]}>
                        {this.props.children}
                    </View>
                </ScrollView>
                {
                    loading &&
                    <View style={styles.loading}>
                        <ActivityIndicator size='large' />
                    </View>
                }
            </View>
        );
    }
}

interface IStyles {
    loading: ViewStyle;
    adjustmentContainer: ViewStyle;
    scrollView: ViewStyle;
    container: ViewStyle;
}

const styles = StyleSheet.create<IStyles>({
    loading: {
        position: 'absolute',
        left: 0,
        right: 0,
        top: 0,
        bottom: 0,
        alignItems: 'center',
        justifyContent: 'center',
        backgroundColor: CONST_COLORS.BLURRED.color
    },
    adjustmentContainer: {
        position: 'absolute',
        top: 0,
        right: 0,
        bottom: 0,
        left: 0
    },
    scrollView: {
        flex: 1
    },
    container: {
        flex: 1,
        paddingTop: StatusBar.currentHeight ? StatusBar.currentHeight  + 10 : 50,
        paddingBottom: 70,
        paddingLeft: 33,
        paddingRight: 33
    }
});

