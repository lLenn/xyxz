import React from 'react';
import { View, ViewStyle } from 'react-native';


// flexDirection - Determines the primary axis as ‘row’ or ‘column’
//   row             Default value. The flexible items are displayed horizontally, as a row
//   row-reverse     Same as row, but in reverse order
//   column          The flexible items are displayed vertically, as a column
//   column-reverse  Same as column, but in reverse order

// justifyContent - Determines distribution of children along primary axis
//   flex-start      Default value. Items are positioned at the beginning of the container
//   flex-end        Items are positioned at the end of the container
//   center          Items are positioned at the center of the container
//   space-between   Items are positioned with space between the lines (items are evenly distributed in the line; first item is on the start line, last item on the end line)
//   space-around    Items are positioned with space before, between, and after the lines (items are evenly distributed in the line with equal space around them)
//   space-evenly    Items are distributed so that the spacing between any two adjacent alignment subjects, before the first alignment subject, and after the last alignment subject is the same

// alignItems - determines the alignment of children along the secondary axis
//   stretch         Default value. Items are stretched to fit the container (still respect min-width/max-width)
//   center          Items are positioned at the center of the container
//   flex-start      Items are positioned at the beginning of the container
//   flex-end        Items are positioned at the end of the container
//   baseline        Items are positioned at the baseline of the container
export interface IFlexView {
    flagIsScreenContainer?: boolean; // Indicates its the main view of a screen.
    flex?: number;
    flexDirection?: "row" | "row-reverse" | "column" | "column-reverse";
    justifyContent?: "flex-start" | "center" | "flex-end" | "space-around" | "space-between" | "space-evenly";
    alignItems?: "flex-start" | "center" | "flex-end" | "stretch";
    viewStyle?: ViewStyle;
}

export class CFlexView extends React.Component<IFlexView> {
    static defaultProps = {
        flexDirection: "column",
        justifyContent: "flex-start",
        alignItems: "stretch"
    };

    constructor(pProps:IFlexView & View) {
        super(pProps);
    }

    render() {
        const style:ViewStyle = {
            ...CFlexView.defaultProps,
            flex: this.props.flex,
            flexDirection: this.props.flexDirection,
            justifyContent: this.props.justifyContent,
            alignItems: this.props.alignItems,
            ...(this.props.flagIsScreenContainer && { flex: 1, position: 'relative' })
        };

        return (
            <View style={{ ...this.props.viewStyle, ...style }} {...this.props}>
                {
                    this.props.children
                }
            </View>
        );
    }
}
