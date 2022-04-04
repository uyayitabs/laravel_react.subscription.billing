import React from "react";
import classnames from "classnames";

import { Container, Row, Col, Card, ListGroup, ListGroupItem, TabContent, TabPane, Table, CardHeader, CardTitle, CardBody } from "reactstrap";

import CaptureSubscriber from './CaptureSubscriber';
import ChangePackage from './ChangePackage';
import ChangeAddress from './ChangeAddress';
import SwopSmartcard from './SwopSmartcard';
import ReAuthSmartcard from './ReAuthSmartcard';
import CreateMyAccount from './CreateMyAccount';
import ChangeMyAccount from './ChangeMyAccount';
import Disconnect from './Disconnect';
import RemoveMyAccount from './RemoveMyAccount';
import Reconnect from './Reconnect';
import CloseAccount from './CloseAccount';
import ResetPin from './ResetPin';
import UpdateTransactionStatus from './UpdateTransactionStatus';
import SetLineProperties from './SetLineProperties';
import GetCustomerInfo from './GetCustomerInfo';

class M7Interface extends React.Component {
    constructor(props) {
        super(props)

        this.state = {
            domain: 'domain-here',
            activeTab: '0',
            addresses: [],
            result: undefined,
            msg: undefined
        };

        this.toggle = this.toggle.bind(this);
        this.update = this.update.bind(this);
        this.loading = this.loading.bind(this);

        this.tabs = [
            {
                title: 'Capture Subscriber',
                component: <CaptureSubscriber update={this.update} loading={this.loading} />
            },
            {
                title: 'Change Package',
                component: <ChangePackage update={this.update} loading={this.loading} />
            },
            {
                title: 'Change Address',
                component: <ChangeAddress update={this.update} loading={this.loading} />
            },
            {
                title: 'Swop Smartcard',
                component: <SwopSmartcard update={this.update} loading={this.loading} />
            },
            {
                title: 'Re Auth Smartcard',
                component: <ReAuthSmartcard update={this.update} loading={this.loading} />
            },
            {
                title: 'Create My Account',
                component: <CreateMyAccount update={this.update} loading={this.loading} />
            },
            {
                title: 'Change My Account',
                component: <ChangeMyAccount update={this.update} loading={this.loading} />
            },
            {
                title: 'Remove My Account',
                component: <RemoveMyAccount update={this.update} loading={this.loading} />
            },
            {
                title: 'Disconnect',
                component: <Disconnect update={this.update} loading={this.loading} />
            },
            {
                title: 'Reconnect',
                component: <Reconnect update={this.update} loading={this.loading} />
            },
            {
                title: 'Close Account',
                component: <CloseAccount update={this.update} loading={this.loading} />
            },
            {
                title: 'Reset Pin',
                component: <ResetPin update={this.update} loading={this.loading} />
            },
            {
                title: 'Update Transaction Status',
                component: <UpdateTransactionStatus update={this.update} loading={this.loading} />
            },
            {
                title: 'Set Line Properties',
                component: <SetLineProperties update={this.update} loading={this.loading} />
            },
            {
                title: 'Get Customer Info',
                component: <GetCustomerInfo update={this.update} loading={this.loading} />
            }
        ];
    }

    toggle(tab) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab });
        }
    }

    loading() {
        this.setState({
            result: undefined
        });
    }

    update(data) {
        console.log(data)
        this.setState({
            result: data.data !== undefined ? data.data : data
        });
    }

    prettifyXml(sourceXml) {
        var xmlDoc = new DOMParser().parseFromString(sourceXml, 'application/xml');
        var xsltDoc = new DOMParser().parseFromString([
            // describes how we want to modify the XML - indent everything
            '<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform">',
            '  <xsl:strip-space elements="*"/>',
            '  <xsl:template match="para[content-style][not(text())]">', // change to just text() to strip space in text nodes
            '    <xsl:value-of select="normalize-space(.)"/>',
            '  </xsl:template>',
            '  <xsl:template match="node()|@*">',
            '    <xsl:copy><xsl:apply-templates select="node()|@*"/></xsl:copy>',
            '  </xsl:template>',
            '  <xsl:output indent="yes"/>',
            '</xsl:stylesheet>',
        ].join('\n'), 'application/xml');

        var xsltProcessor = new XSLTProcessor();
        xsltProcessor.importStylesheet(xsltDoc);
        var resultDoc = xsltProcessor.transformToDocument(xmlDoc);
        var resultXml = new XMLSerializer().serializeToString(resultDoc);
        return resultXml;
    };

    render() {
        const CustomerDTOInfo = (props) => {
            return (
                <Card>
                    <CardHeader>
                        <CardTitle tag="h5" className="mb-0 float-left">CustomerDTOInfo</CardTitle>
                    </CardHeader>
                    <CardBody>
                        <h5>BankingInformation</h5>
                        <Table className="mb-0">
                            <tbody>
                                <tr>
                                    <td>AccountName: </td>
                                    <td>{props.info.BankingInformation.AccountName}</td>
                                </tr>
                                <tr>
                                    <td>BIC: </td>
                                    <td>{props.info.BankingInformation.BIC}</td>
                                </tr>
                                <tr>
                                    <td>IBAN: </td>
                                    <td>{props.info.BankingInformation.IBAN}</td>
                                </tr>
                            </tbody>
                        </Table>
                    </CardBody>
                    <CardBody>
                        <h5>BillingAddress</h5>
                        <Table className="mb-0">
                            <tbody>
                                <tr>
                                    <td>HouseNumber: </td>
                                    <td>{props.info.BillingAddress.HouseNumber}</td>
                                </tr>
                                <tr>
                                    <td>HouseNumberExtension: </td>
                                    <td>{props.info.BillingAddress.HouseNumberExtension}</td>
                                </tr>
                                <tr>
                                    <td>Street: </td>
                                    <td>{props.info.BillingAddress.Street}</td>
                                </tr>
                                <tr>
                                    <td>Municipality: </td>
                                    <td>{props.info.BillingAddress.Municipality}</td>
                                </tr>
                                <tr>
                                    <td>City: </td>
                                    <td>{props.info.BillingAddress.City}</td>
                                </tr>
                                <tr>
                                    <td>State: </td>
                                    <td>{props.info.BillingAddress.State}</td>
                                </tr>
                                <tr>
                                    <td>Country: </td>
                                    <td>{props.info.BillingAddress.Country}</td>
                                </tr>
                                <tr>
                                    <td>PostalCode: </td>
                                    <td>{props.info.BillingAddress.PostalCode}</td>
                                </tr>
                            </tbody>
                        </Table>
                    </CardBody>
                    <CardBody>
                        <h5>BillingCustomerDetails</h5>
                        <Table className="mb-0">
                            <tbody>
                                <tr>
                                    <td>DateOfBirth: </td>
                                    <td>{props.info.BillingCustomerDetails.DateOfBirth}</td>
                                </tr>
                                <tr>
                                    <td>Email: </td>
                                    <td>{props.info.BillingCustomerDetails.Email}</td>
                                </tr>
                                <tr>
                                    <td>Title: </td>
                                    <td>{props.info.BillingCustomerDetails.Title}</td>
                                </tr>
                                <tr>
                                    <td>FirstName: </td>
                                    <td>{props.info.BillingCustomerDetails.FirstName}</td>
                                </tr>
                                <tr>
                                    <td>MiddleName: </td>
                                    <td>{props.info.BillingCustomerDetails.MiddleName}</td>
                                </tr>
                                <tr>
                                    <td>SurName: </td>
                                    <td>{props.info.BillingCustomerDetails.SurName}</td>
                                </tr>
                                <tr>
                                    <td>Initials: </td>
                                    <td>{props.info.BillingCustomerDetails.Initials}</td>
                                </tr>
                                <tr>
                                    <td>Gender: </td>
                                    <td>{props.info.BillingCustomerDetails.Gender}</td>
                                </tr>
                                <tr>
                                    <td>Mobile: </td>
                                    <td>{props.info.BillingCustomerDetails.Mobile}</td>
                                </tr>
                                <tr>
                                    <td>Phone: </td>
                                    <td>{props.info.BillingCustomerDetails.Phone}</td>
                                </tr>
                            </tbody>
                        </Table>
                    </CardBody>
                    <CardBody>
                        <Table className="mb-0">
                            <tbody>
                                <tr>
                                    <td>Tenant: </td>
                                    <td>{props.info.Tenant}</td>
                                </tr>
                                <tr>
                                    <td>ContractEndDate: </td>
                                    <td>{props.info.ContractEndDate}</td>
                                </tr>
                                <tr>
                                    <td>ContractNumber: </td>
                                    <td>{props.info.ContractNumber}</td>
                                </tr>
                                <tr>
                                    <td>ContractPeriod: </td>
                                    <td>{props.info.ContractPeriod}</td>
                                </tr>
                                <tr>
                                    <td>ContractStartDate: </td>
                                    <td>{props.info.ContractStartDate}</td>
                                </tr>
                                <tr>
                                    <td>CustomerNumber: </td>
                                    <td>{props.info.CustomerNumber}</td>
                                </tr>
                                <tr>
                                    <td>DealerNumber: </td>
                                    <td>{props.info.DealerNumber}</td>
                                </tr>
                                <tr>
                                    <td>OptedForNewsletter: </td>
                                    <td>{props.info.OptedForNewsletter}</td>
                                </tr>
                            </tbody>
                        </Table>
                    </CardBody>
                    <CardBody>
                        <h5>SmartcardPackages</h5>
                        <Table className="mb-0">
                            <tbody>
                                <tr>
                                    <td>SmartcardPackagesDTO: </td>
                                    <td></td>
                                </tr>
                                {
                                    props.info.SmartcardPackages.SmartcardPackagesDTO.map((item, index) => {
                                        return (
                                            <React.Fragment key={index}>
                                                <tr>
                                                    <td>DecoderNumber: </td>
                                                    <td>{item.DecoderNumber}</td>
                                                </tr>
                                                <tr>
                                                    <td>MainSmartcard: </td>
                                                    <td>{item.MainSmartcard}</td>
                                                </tr>
                                                <tr>
                                                    <td>ProductInfo: </td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>ProductId: </td>
                                                    <td>{item.ProductInfo.ProductInfo.ProductId}</td>
                                                </tr>
                                                <tr>
                                                    <td>ProductName: </td>
                                                    <td>{item.ProductInfo.ProductInfo.ProductName}</td>
                                                </tr>
                                                <tr>
                                                    <td>ProductStatus: </td>
                                                    <td>{item.ProductInfo.ProductInfo.ProductStatus}</td>
                                                </tr>
                                                <tr>
                                                    <td>SmartcardNumber: </td>
                                                    <td>{item.SmartcardNumber}</td>
                                                </tr>
                                            </React.Fragment>
                                        )
                                    })
                                }
                                <tr>
                                    <td>TransactionType: </td>
                                    <td>{props.info.TransactionType}</td>
                                </tr>
                                <tr>
                                    <td>WishDate: </td>
                                    <td>{props.info.WishDate}</td>
                                </tr>
                            </tbody>
                        </Table>
                    </CardBody>
                </Card>
            )
        }
        return (
            <Container fluid className="p-0">
                <h1 className="h3 mb-3">M7 Interface</h1>

                <Row>
                    <Col md="3" xl="2">
                        <Card>
                            <ListGroup tabs="true">
                                {
                                    this.tabs.map((item, index) => {
                                        return (
                                            <ListGroupItem
                                                key={index}
                                                className={classnames({ active: this.state.activeTab === index.toString() })}
                                                onClick={() => { this.toggle(index.toString()) }}
                                            >
                                                {item.title}
                                            </ListGroupItem>
                                        )
                                    })
                                }
                            </ListGroup>
                        </Card>
                    </Col>
                    <Col md="9" xl="10">
                        <TabContent activeTab={this.state.activeTab}>
                            {
                                this.tabs.map((item, index) => {
                                    return (
                                        <TabPane
                                            tabId={index.toString()}
                                            key={index}
                                        >
                                            <Card>
                                                {item.component}
                                            </Card>
                                        </TabPane>
                                    )
                                })
                            }
                        </TabContent>
                    </Col>
                </Row>
                <Row>
                    {this.state.result ?
                        <Col lg="12">
                            <Card className="tabs-m7 tab-vertical">
                                <Table className="mb-0">
                                    <tbody>
                                        <tr>
                                            <td>{this.state.result.error == undefined ? this.state.result['Result'] : 'Error'}:</td>
                                            <td>{this.state.result.error == undefined ? (this.state.result['Exception'] != null ? this.state.result['Exception'] : this.state.result['ResultDescription']) : this.state.result.msg}</td>
                                        </tr>
                                        <tr>
                                            <td>Xml: </td>
                                            <td>{this.prettifyXml(this.state.result.xml)}</td>
                                        </tr>
                                    </tbody>
                                </Table>
                            </Card>
                        </Col>
                        : ''}
                </Row>
                <Row>
                    {this.state.result && this.state.result.CustomerDTOInfo ? <Col lg="6"><CustomerDTOInfo info={this.state.result.CustomerDTOInfo} /></Col> : ''}
                </Row>
            </Container>
        )
    }
}

export default M7Interface;
