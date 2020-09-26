import React from 'react';
import { Platform, StatusBarIOS, View, NativeModules, LayoutChangeEvent } from 'react-native';


export class CStatusBarSpacer extends React.Component<{ onLayout:(pEvent:LayoutChangeEvent)=>void }> {
    state = {
        height: 20
    };

    componentDidMount() {
        // TODO: implement status height bar change for android (with StatusBar, but can't find the proper event that's fired in docs)
        if(Platform.OS === "ios") {
            StatusBarIOS.addListener("statusBarFrameDidChange", (pStatusBarData:any) => {
                this.setState({ height: pStatusBarData.frame.height });
            });
        }

        this.getStatusBarHeight().then((pHeight:number) => {
            this.setState({ height: pHeight });
        });
    }

    render() {
        return (<View onLayout={this.props.onLayout} style={{ height: this.state.height }}/>);
    }

    private getStatusBarHeight():Promise<number> {
        return new Promise(function(resolve, reject) {
            try {
                if(Platform.OS === "android") {
                    resolve(NativeModules.StatusBarManager.HEIGHT);
                }
                else if(Platform.OS === "ios") {
                    NativeModules.StatusBarManager.getHeight((pStatusBarData:any) => {
                        resolve(pStatusBarData.height);
                    });
                }
            }
            catch(e) {
                reject(e);
            }
        });
    }
}
