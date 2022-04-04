import React from "react";
import { Col, Card, CardBody, Media, Row } from "reactstrap";

import { Globe, User, CreditCard, File, Tag } from "react-feather";

const Statistics = () => (
  <Row>
    <Col md="6" xl>
      <Card className="flex-fill">
        <CardBody className="py-4">
          <Media>
            <div className="d-inline-block mt-2 mr-3">
              <Globe className="feather-lg text-primary" />
            </div>
            <Media body>
              <h3 className="mb-2">2,391</h3>
              <div className="mb-0">Tenants</div>
            </Media>
          </Media>
        </CardBody>
      </Card>
    </Col>
    <Col md="6" xl>
      <Card className="flex-fill">
        <CardBody className="py-4">
          <Media>
            <div className="d-inline-block mt-2 mr-3">
              <User className="feather-lg text-warning" />
            </div>
            <Media body>
              <h3 className="mb-2">8,521</h3>
              <div className="mb-0">Persons</div>
            </Media>
          </Media>
        </CardBody>
      </Card>
    </Col>
    <Col md="6" xl>
      <Card className="flex-fill">
        <CardBody className="py-4">
          <Media>
            <div className="d-inline-block mt-2 mr-3">
              <CreditCard className="feather-lg text-success" />
            </div>
            <Media body>
              <h3 className="mb-2">5,954</h3>
              <div className="mb-0">Subscriptions</div>
            </Media>
          </Media>
        </CardBody>
      </Card>
    </Col>
    <Col md="6" xl>
      <Card className="flex-fill">
        <CardBody className="py-4">
          <Media>
            <div className="d-inline-block mt-2 mr-3">
              <File className="feather-lg text-danger" />
            </div>
            <Media body>
              <h3 className="mb-2">9,396</h3>
              <div className="mb-0">Invoices</div>
            </Media>
          </Media>
        </CardBody>
      </Card>
    </Col>
    <Col md="6" xl className="d-none d-xxl-flex">
      <Card className="flex-fill">
        <CardBody className="py-4">
          <Media>
            <div className="d-inline-block mt-2 mr-3">
              <Tag className="feather-lg text-info" />
            </div>
            <Media body>
              <h3 className="mb-2">1,502</h3>
              <div className="mb-0">Products</div>
            </Media>
          </Media>
        </CardBody>
      </Card>
    </Col>
  </Row>
);

export default Statistics;
